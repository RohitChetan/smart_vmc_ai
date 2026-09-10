<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplaintRequest;
use App\Models\Complaint;
use App\Models\ComplaintAIAnalysis;
use App\Services\WardLocator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    /**
     * Store a new citizen complaint.
     */
    public function store(
        StoreComplaintRequest $request,
        WardLocator $wardLocator
    ): JsonResponse {
        $data = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Detect Ward Automatically From GPS
        |--------------------------------------------------------------------------
        */

        $ward = $wardLocator->findWard(
            (float) $data['latitude'],
            (float) $data['longitude']
        );

        if (!$ward) {
            return response()->json([
                'success' => false,
                'message' =>
                    'The selected location is outside VMC ward boundaries.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Create Complaint
        |--------------------------------------------------------------------------
        */

        $complaint = DB::transaction(function () use (
            $request,
            $data,
            $ward
        ) {
            $complaint = Complaint::create([
                'complaint_number' =>
                    $this->generateComplaintNumber(),

                /*
                |--------------------------------------------------------------------------
                | Temporary Guest Support
                |--------------------------------------------------------------------------
                | Authentication will be connected later.
                |--------------------------------------------------------------------------
                */

                'user_id' =>
                    $request->user()?->id
                    ?? \App\Models\User::query()
                        ->firstOrFail()
                        ->id,

                /*
                |--------------------------------------------------------------------------
                | Optional User Category
                |--------------------------------------------------------------------------
                */

                'user_category_id' =>
                    $data['category_id'] ?? null,

                /*
                |--------------------------------------------------------------------------
                | Automatically Detected Ward
                |--------------------------------------------------------------------------
                */

                'ward_id' => $ward->id,

                /*
                |--------------------------------------------------------------------------
                | GPS
                |--------------------------------------------------------------------------
                */

                'latitude' =>
                    $data['latitude'],

                'longitude' =>
                    $data['longitude'],

                'location_accuracy' =>
                    $data['location_accuracy'] ?? null,

                /*
                |--------------------------------------------------------------------------
                | Complaint
                |--------------------------------------------------------------------------
                */

                'description' =>
                    $data['description'],

                /*
                |--------------------------------------------------------------------------
                | Initial State
                |--------------------------------------------------------------------------
                */

                'priority' => 'medium',

                'status' => 'ai_processing',

                'submitted_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Store Complaint Media
            |--------------------------------------------------------------------------
            */

            foreach (
                $request->file('media', [])
                as $file
            ) {
                $path = $file->store(
                    'complaints/' . $complaint->id,
                    'public'
                );

                $type = Str::startsWith(
                    $file->getMimeType(),
                    'video/'
                )
                    ? 'video'
                    : 'image';

                $complaint->media()->create([
                    'type' =>
                        $type,

                    'file_path' =>
                        $path,

                    'mime_type' =>
                        $file->getMimeType(),

                    'file_size' =>
                        $file->getSize(),
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Create AI Analysis Job
            |--------------------------------------------------------------------------
            */

            ComplaintAIAnalysis::create([
                'complaint_id' =>
                    $complaint->id,

                'status' =>
                    'pending',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Initial Status History
            |--------------------------------------------------------------------------
            */

            $complaint->statusHistory()->create([
                'status' =>
                    'ai_processing',

                'changed_by' =>
                    null,

                'remarks' =>
                    'Complaint submitted and sent to local AI worker for analysis.',
            ]);

            return $complaint;
        });

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'message' =>
                'Complaint submitted successfully.',

            'complaint' => [
                'id' =>
                    $complaint->id,

                'complaint_number' =>
                    $complaint->complaint_number,

                'status' =>
                    $complaint->status,

                'priority' =>
                    $complaint->priority,

                'ward' => [
                    'id' =>
                        $ward->id,

                    'ward_no' =>
                        $ward->ward_no,

                    'name' =>
                        $ward->name,
                ],

                'location' => [
                    'latitude' =>
                        $complaint->latitude,

                    'longitude' =>
                        $complaint->longitude,

                    'accuracy' =>
                        $complaint->location_accuracy,
                ],

                'ai_status' =>
                    'pending',
            ],
        ], 201);
    }

    /**
     * Track a complaint by complaint number.
     */
    public function show(
        string $complaintNumber
    ): JsonResponse {
        /*
        |--------------------------------------------------------------------------
        | Load Complete Complaint Data
        |--------------------------------------------------------------------------
        */

        $complaint = Complaint::with([
            'ward',
            'userCategory',
            'aiCategory',
            'department',
            'media',
            'aiAnalyses',
            'assignments.assignedTo',
            'statusHistory',
        ])
            ->where(
                'complaint_number',
                $complaintNumber
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Complaint Not Found
        |--------------------------------------------------------------------------
        */

        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Complaint not found.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Latest AI Analysis
        |--------------------------------------------------------------------------
        */

        $aiAnalysis = $complaint->aiAnalyses
            ->sortByDesc('id')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Latest Assignment
        |--------------------------------------------------------------------------
        */

        $assignment = $complaint->assignments
            ->sortByDesc('id')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'complaint' => [

                /*
                |--------------------------------------------------------------------------
                | Basic Complaint
                |--------------------------------------------------------------------------
                */

                'id' =>
                    $complaint->id,

                'complaint_number' =>
                    $complaint->complaint_number,

                'description' =>
                    $complaint->description,

                'status' =>
                    $complaint->status,

                'priority' =>
                    $complaint->priority,

                'submitted_at' =>
                    $complaint->submitted_at?->toISOString(),

                'due_at' =>
                    $complaint->due_at?->toISOString(),

                'resolved_at' =>
                    $complaint->resolved_at?->toISOString(),

                'closed_at' =>
                    $complaint->closed_at?->toISOString(),

                /*
                |--------------------------------------------------------------------------
                | AI Information
                |--------------------------------------------------------------------------
                */

                'ai_confidence' =>
                    $complaint->ai_confidence,

                'ai_decision_reason' =>
                    $complaint->ai_decision_reason,

                /*
                |--------------------------------------------------------------------------
                | Category
                |--------------------------------------------------------------------------
                */

                'category' => [
                    'user_selected' =>
                        $complaint->userCategory?->name,

                    'ai_detected' =>
                        $complaint->aiCategory?->name,
                ],

                /*
                |--------------------------------------------------------------------------
                | Department
                |--------------------------------------------------------------------------
                */

                'department' =>
                    $complaint->department?->name,

                /*
                |--------------------------------------------------------------------------
                | Ward
                |--------------------------------------------------------------------------
                */

                'ward' => [
                    'id' =>
                        $complaint->ward?->id,

                    'ward_no' =>
                        $complaint->ward?->ward_no,

                    'name' =>
                        $complaint->ward?->name,
                ],

                /*
                |--------------------------------------------------------------------------
                | GPS Location
                |--------------------------------------------------------------------------
                */

                'location' => [
                    'latitude' =>
                        $complaint->latitude,

                    'longitude' =>
                        $complaint->longitude,

                    'accuracy' =>
                        $complaint->location_accuracy,
                ],

                /*
                |--------------------------------------------------------------------------
                | Media
                |--------------------------------------------------------------------------
                */

                'media' =>
                    $complaint->media
                        ->map(function ($media) {
                            return [
                                'id' =>
                                    $media->id,

                                'type' =>
                                    $media->type,

                                'file_path' =>
                                    $media->file_path,

                                'mime_type' =>
                                    $media->mime_type,
                            ];
                        })
                        ->values(),

                /*
                |--------------------------------------------------------------------------
                | Latest AI Analysis
                |--------------------------------------------------------------------------
                */

                'ai_analysis' =>
                    $aiAnalysis
                        ? [
                            'id' =>
                                $aiAnalysis->id,

                            'model_name' =>
                                $aiAnalysis->model_name,

                            'model_version' =>
                                $aiAnalysis->model_version,

                            'predicted_category' =>
                                $aiAnalysis->predicted_category,

                            'confidence' =>
                                $aiAnalysis->confidence,

                            'detections' =>
                                $aiAnalysis->detections,

                            'raw_result' =>
                                $aiAnalysis->raw_result,

                            'status' =>
                                $aiAnalysis->status,

                            'error_message' =>
                                $aiAnalysis->error_message,

                            'created_at' =>
                                $aiAnalysis->created_at?->toISOString(),

                            'updated_at' =>
                                $aiAnalysis->updated_at?->toISOString(),
                        ]
                        : null,

                /*
                |--------------------------------------------------------------------------
                | Status Timeline
                |--------------------------------------------------------------------------
                */

                'status_history' =>
                    $complaint->statusHistory
                        ->sortBy('created_at')
                        ->values()
                        ->map(function ($history) {
                            return [
                                'status' =>
                                    $history->status,

                                'remarks' =>
                                    $history->remarks,

                                'created_at' =>
                                    $history->created_at
                                        ?->toISOString(),
                            ];
                        }),

                /*
                |--------------------------------------------------------------------------
                | Assignment
                |--------------------------------------------------------------------------
                */

                'assignment' =>
                    $assignment
                        ? [
                            'id' =>
                                $assignment->id,

                            'assigned_to' => [
                                'id' =>
                                    $assignment->assignedTo?->id,

                                'name' =>
                                    $assignment->assignedTo?->name,

                                'email' =>
                                    $assignment->assignedTo?->email,
                            ],

                            'assigned_at' =>
                                $assignment->assigned_at
                                    ?->toISOString(),

                            'completed_at' =>
                                $assignment->completed_at
                                    ?->toISOString(),

                            'notes' =>
                                $assignment->notes,
                        ]
                        : null,
            ],
        ]);
    }

    /**
     * Generate unique complaint number.
     */
    private function generateComplaintNumber(): string
    {
        do {
            $number =
                'SVC-' .
                now()->format('Y') .
                '-' .
                strtoupper(
                    Str::random(8)
                );

        } while (
            Complaint::where(
                'complaint_number',
                $number
            )->exists()
        );

        return $number;
    }
}