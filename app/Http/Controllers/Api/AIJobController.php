<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ComplaintAIAnalysis;
use App\Models\ComplaintStatusHistory;
use App\Services\IncidentAssignmentService;
use App\Services\IncidentClusteringService;
use App\Services\WardLocator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\Notifications\NotificationService;

class AIJobController extends Controller
{
    /**
     * Get pending AI jobs.
     */
    public function pending(): JsonResponse
    {
        $jobs = ComplaintAIAnalysis::with([
            'complaint.media',
            'complaint.ward',
            'complaint.userCategory',
        ])
            ->where('status', 'pending')
            ->orderBy('id')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'count' => $jobs->count(),

            'jobs' => $jobs->map(function ($job) {
                return [
                    'analysis_id' => $job->id,

                    'complaint' => [
                        'id' => $job->complaint->id,

                        'complaint_number' =>
                            $job->complaint->complaint_number,

                        'description' =>
                            $job->complaint->description,

                        'location' => [
                            'latitude' =>
                                $job->complaint->latitude,

                            'longitude' =>
                                $job->complaint->longitude,
                        ],

                        'ward' => [
                            'id' =>
                                $job->complaint->ward?->id,

                            'ward_no' =>
                                $job->complaint->ward?->ward_no,

                            'name' =>
                                $job->complaint->ward?->name,
                        ],

                        'user_category' => [
                            'id' =>
                                $job->complaint->userCategory?->id,

                            'name' =>
                                $job->complaint->userCategory?->name,
                        ],

                        'media' => $job->complaint->media
                            ->map(function ($media) {
                                return [
                                    'id' => $media->id,
                                    'type' => $media->type,
                                    'file_path' => $media->file_path,
                                    'mime_type' => $media->mime_type,
                                ];
                            })
                            ->values(),
                    ],
                ];
            })->values(),
        ]);
    }

    /**
     * Save AI result and route complaint.
     */
    public function result(
        Request $request,
        int $analysisId,
        IncidentClusteringService $incidentService,
        IncidentAssignmentService $incidentAssignmentService,
        WardLocator $wardLocator,
        NotificationService $notificationService
    ): JsonResponse {
        $data = $request->validate([
            'predicted_category' => [
                'required',
                'string',
                'max:255',
            ],

            'confidence' => [
                'required',
                'numeric',
                'between:0,1',
            ],

            'priority' => [
                'required',
                'in:low,medium,high,critical',
            ],

            'reason' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'detections' => [
                'nullable',
                'array',
            ],

            'raw_result' => [
                'nullable',
                'array',
            ],

            'model_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'model_version' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Load AI Analysis
        |--------------------------------------------------------------------------
        */

        $analysis = ComplaintAIAnalysis::with([
            'complaint',
        ])->findOrFail($analysisId);

        $complaint = $analysis->complaint;

        /*
        |--------------------------------------------------------------------------
        | Find AI Category
        |--------------------------------------------------------------------------
        */

        $category = Category::query()
            ->where('name', $data['predicted_category'])
            ->where('is_active', true)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'AI predicted category not found.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Auto Detect Ward From GPS
        |--------------------------------------------------------------------------
        */

        $detectedWard = null;

        if (
            $complaint->latitude !== null &&
            $complaint->longitude !== null
        ) {
            $detectedWard = $wardLocator->findWard(
                (float) $complaint->latitude,
                (float) $complaint->longitude
            );
        }

        if (!$detectedWard) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Unable to determine ward from complaint GPS location.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Save AI Analysis
        |--------------------------------------------------------------------------
        */

        $analysis->update([
            'model_name' =>
                $data['model_name'] ?? 'local-ai',

            'model_version' =>
                $data['model_version'] ?? '1.0',

            'predicted_category' =>
                $data['predicted_category'],

            'confidence' =>
                $data['confidence'],

            'detections' =>
                $data['detections'] ?? null,

            'raw_result' =>
                $data['raw_result'] ?? null,

            'status' =>
                'completed',

            'error_message' =>
                null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Save Complaint AI Decision + GIS Ward
        |--------------------------------------------------------------------------
        */

        $complaint->update([
            'ai_category_id' =>
                $category->id,

            'department_id' =>
                $category->department_id,

            'ward_id' =>
                $detectedWard->id,

            'priority' =>
                $data['priority'],

            'ai_confidence' =>
                $data['confidence'],

            'ai_decision_reason' =>
                $data['reason'] ?? null,

            'status' =>
                'ai_processing',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Find / Create Civic Incident
        |--------------------------------------------------------------------------
        */

        $incident = $incidentService->attachComplaint(
            $complaint->fresh()
        );

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT: Sync Complaint Ward From Incident
        |--------------------------------------------------------------------------
        |
        | Incident clustering can attach the complaint to an existing
        | incident. Therefore the final source of truth is the incident ward.
        |
        */

        if ($incident->ward_id) {
            $complaint->update([
                'ward_id' => $incident->ward_id,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Incident-Level Assignment
        |--------------------------------------------------------------------------
        |
        | One active field assignment per civic incident.
        |
        | We first check whether an active assignment already exists.
        | This prevents duplicate notifications when multiple complaints
        | are clustered into the same incident.
        |
        */

        $hadActiveAssignment =
            $incidentAssignmentService->hasActiveAssignment(
                $incident->fresh()
            );

        $incidentAssignment =
            $incidentAssignmentService->assign(
                $incident->fresh()
            );

        /*
        |--------------------------------------------------------------------------
        | Notify Newly Assigned Ward Officer
        |--------------------------------------------------------------------------
        |
        | Send notification only when a NEW active assignment was created.
        | Existing assignments will not receive duplicate notifications.
        |
        */

        if (
            !$hadActiveAssignment &&
            $incidentAssignment->assignedTo
        ) {
            $notificationService->send(
                event: 'complaint_assigned',
                user: $incidentAssignment->assignedTo,
                complaint: $complaint,
                data: [
                    'title' => 'New Civic Complaint Assigned',

                    'message' => sprintf(
                        '%s has been assigned to you for Ward %s.',
                        $incident->incident_number,
                        $incident->ward?->ward_no ?? 'Unknown'
                    ),

                    'url' => '/field/dashboard',

                    'data' => [
                        'incident_id' =>
                            $incident->id,

                        'incident_number' =>
                            $incident->incident_number,

                        'complaint_id' =>
                            $complaint->id,

                        'complaint_number' =>
                            $complaint->complaint_number,

                        'ward_id' =>
                            $incident->ward_id,

                        'ward_no' =>
                            $incident->ward?->ward_no,

                        'priority' =>
                            $incident->priority,

                        'category' =>
                            $incident->category?->name,

                        'department' =>
                            $incident->department?->name,
                    ],
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Move Complaint To Assigned
        |--------------------------------------------------------------------------
        */

        $complaint->update([
            'status' => 'assigned',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Status History
        |--------------------------------------------------------------------------
        */

        ComplaintStatusHistory::create([
            'complaint_id' =>
                $complaint->id,

            'status' =>
                'assigned',

            'changed_by' =>
                null,

            'remarks' =>
                'Complaint automatically assigned by AI and incident routing engine.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Refresh Relations For Response
        |--------------------------------------------------------------------------
        */

        $complaint->refresh();

        $incident->load([
            'category',
            'department',
            'ward',
            'assignments.assignedTo',
        ]);

        $incidentAssignment->load([
            'assignedTo',
            'assignedBy',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'message' =>
                'AI result saved and incident routed successfully.',

            'complaint' => [
                'id' =>
                    $complaint->id,

                'complaint_number' =>
                    $complaint->complaint_number,

                'status' =>
                    $complaint->status,

                'ward' => [
                    'id' =>
                        $complaint->ward?->id,

                    'ward_no' =>
                        $complaint->ward?->ward_no,

                    'name' =>
                        $complaint->ward?->name,
                ],

                'ai_category' => [
                    'id' =>
                        $category->id,

                    'name' =>
                        $category->name,
                ],

                'department' => [
                    'id' =>
                        $category->department?->id,

                    'name' =>
                        $category->department?->name,
                ],

                'priority' =>
                    $complaint->priority,

                'confidence' =>
                    $complaint->ai_confidence,

                'ai_decision_reason' =>
                    $complaint->ai_decision_reason,
            ],

            'incident' => [
                'id' =>
                    $incident->id,

                'incident_number' =>
                    $incident->incident_number,

                'title' =>
                    $incident->title,

                'report_count' =>
                    $incident->report_count,

                'status' =>
                    $incident->status,

                'priority' =>
                    $incident->priority,

                'ward' => [
                    'id' =>
                        $incident->ward?->id,

                    'ward_no' =>
                        $incident->ward?->ward_no,

                    'name' =>
                        $incident->ward?->name,
                ],
            ],

            'assigned_officer' => [
                'id' =>
                    $incidentAssignment->assignedTo?->id,

                'name' =>
                    $incidentAssignment->assignedTo?->name,

                'email' =>
                    $incidentAssignment->assignedTo?->email,
            ],

            'assignment' => [
                'id' =>
                    $incidentAssignment->id,

                'assigned_to' =>
                    $incidentAssignment->assignedTo?->name,

                'assigned_at' =>
                    $incidentAssignment
                        ->assigned_at
                        ?->toISOString(),

                'completed_at' =>
                    $incidentAssignment
                        ->completed_at
                        ?->toISOString(),
            ],
        ]);
    }

    /**
     * Get AI job media.
     */
    public function media(
        int $analysisId,
        int $mediaId
    ) {
        $analysis = ComplaintAIAnalysis::with([
            'complaint',
        ])->findOrFail($analysisId);

        $media = $analysis->complaint
            ->media()
            ->where('id', $mediaId)
            ->firstOrFail();

        if (
            !Storage::disk('public')
                ->exists($media->file_path)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Media file not found.',
            ], 404);
        }

        return Storage::disk('public')->response(
            $media->file_path,
            basename($media->file_path),
            [
                'Content-Type' =>
                    $media->mime_type,
            ]
        );
    }
}