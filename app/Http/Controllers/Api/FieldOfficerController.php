<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CivicIncident;
use App\Models\IncidentAssignment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FieldOfficerController extends Controller
{
    /**
     * Field Officer Dashboard.
     *
     * Dashboard is incident-based.
     *
     * The authenticated officer can only see incidents:
     *
     * 1. Belonging to the officer's ward.
     * 2. Assigned to the authenticated officer.
     */
    public function dashboard(Request $request): JsonResponse
    {
        /** @var User $officer */
        $officer = $request->user();

        if (!$officer->ward_id) {
            return response()->json([
                'success' => false,
                'message' => 'Field officer is not assigned to a ward.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Get incidents assigned to this officer
        |--------------------------------------------------------------------------
        */

        $incidents = CivicIncident::with([
            'ward',
            'category',
            'department',
            'complaints.aiCategory',
            'complaints.media',
            'complaints.statusHistory',
            'assignments.assignedTo',
        ])
            ->where('ward_id', $officer->ward_id)
            ->whereHas('assignments', function ($query) use ($officer) {
                $query
                    ->where('assigned_to', $officer->id)
                    ->whereNull('completed_at');
            })
            ->orderByRaw("
                CASE priority
                    WHEN 'critical' THEN 1
                    WHEN 'high' THEN 2
                    WHEN 'medium' THEN 3
                    WHEN 'low' THEN 4
                    ELSE 5
                END
            ")
            ->orderBy('due_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $complaints = $incidents->flatMap(
            fn ($incident) => $incident->complaints
        );

        return response()->json([
            'success' => true,

            'officer' => [
                'id' => $officer->id,
                'name' => $officer->name,
                'email' => $officer->email,
                'role' => $officer->role,
                'ward_id' => $officer->ward_id,

                'ward' => $officer->ward ? [
                    'id' => $officer->ward->id,
                    'ward_no' => $officer->ward->ward_no,
                    'name' => $officer->ward->name,
                ] : null,
            ],

            'summary' => [
                'total_incidents' => $incidents->count(),

                'total_complaints' => $complaints->count(),

                'open' => $incidents
                    ->where('status', 'open')
                    ->count(),

                'assigned' => $incidents
                    ->where('status', 'assigned')
                    ->count(),

                'in_progress' => $incidents
                    ->where('status', 'in_progress')
                    ->count(),

                'resolved' => $incidents
                    ->where('status', 'resolved')
                    ->count(),

                'high_priority' => $incidents
                    ->whereIn('priority', ['high', 'critical'])
                    ->count(),
            ],

            /*
            |--------------------------------------------------------------------------
            | Incidents
            |--------------------------------------------------------------------------
            */

            'incidents' => $incidents->map(function ($incident) {

                $assignment = $incident->assignments
                    ->whereNull('completed_at')
                    ->sortByDesc('id')
                    ->first();

                $complaints = $incident->complaints;

                return [
                    'id' =>
                        $incident->id,

                    'incident_number' =>
                        $incident->incident_number,

                    'title' =>
                        $incident->title,

                    'description' =>
                        $incident->description,

                    'status' =>
                        $incident->status,

                    'priority' =>
                        $incident->priority,

                    'report_count' =>
                        $incident->report_count,

                    'duplicate_confidence' =>
                        $incident->duplicate_confidence,

                    'first_reported_at' =>
                        $incident->first_reported_at?->toISOString(),

                    'last_reported_at' =>
                        $incident->last_reported_at?->toISOString(),

                    'due_at' =>
                        $incident->due_at?->toISOString(),

                    'resolved_at' =>
                        $incident->resolved_at?->toISOString(),

                    'category' => [
                        'id' =>
                            $incident->category?->id,

                        'name' =>
                            $incident->category?->name,

                        'slug' =>
                            $incident->category?->slug,
                    ],

                    'department' => [
                        'id' =>
                            $incident->department?->id,

                        'name' =>
                            $incident->department?->name,

                        'code' =>
                            $incident->department?->code,
                    ],

                    'ward' => [
                        'id' =>
                            $incident->ward?->id,

                        'ward_no' =>
                            $incident->ward?->ward_no,

                        'name' =>
                            $incident->ward?->name,
                    ],

                    'location' => [
                        'latitude' =>
                            $incident->latitude,

                        'longitude' =>
                            $incident->longitude,

                        'accuracy' =>
                            $incident->location_accuracy,
                    ],

                    'assignment' => $assignment ? [
                        'id' =>
                            $assignment->id,

                        'assigned_at' =>
                            $assignment->assigned_at?->toISOString(),

                        'started_at' =>
                            $assignment->started_at?->toISOString(),

                        'completed_at' =>
                            $assignment->completed_at?->toISOString(),

                        'notes' =>
                            $assignment->notes,

                        'assigned_to' =>
                            $assignment->assignedTo ? [
                                'id' =>
                                    $assignment->assignedTo->id,

                                'name' =>
                                    $assignment->assignedTo->name,

                                'email' =>
                                    $assignment->assignedTo->email,
                            ] : null,
                    ] : null,

                    /*
                    |--------------------------------------------------------------------------
                    | Citizen Reports
                    |--------------------------------------------------------------------------
                    */

                    'complaints' => $complaints
                        ->map(function ($complaint) {

                            return [
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

                                'ai_confidence' =>
                                    $complaint->ai_confidence,

                                'ai_category' =>
                                    $complaint->aiCategory?->name,

                                'location' => [
                                    'latitude' =>
                                        $complaint->latitude,

                                    'longitude' =>
                                        $complaint->longitude,

                                    'accuracy' =>
                                        $complaint->location_accuracy,
                                ],

                                'media_count' =>
                                    $complaint->media->count(),
                            ];
                        })
                        ->values(),
                ];
            })->values(),
        ]);
    }

    /**
     * Change complaint status.
     *
     * Status changes are performed through the incident assigned
     * to the authenticated officer.
     */
    public function updateStatus(
        Request $request,
        int $complaintId
    ): JsonResponse {
        $data = $request->validate([
            'status' => [
                'required',
                'in:in_progress,resolved',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        /** @var User $officer */
        $officer = $request->user();

        if (!$officer->ward_id) {
            return response()->json([
                'success' => false,
                'message' => 'Field officer is not assigned to a ward.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Find complaint through its incident assignment
        |--------------------------------------------------------------------------
        */

        $complaint = \App\Models\Complaint::with([
            'incident',
            'incident.assignments',
            'ward',
        ])
            ->where('id', $complaintId)
            ->where('ward_id', $officer->ward_id)
            ->whereHas('incident.assignments', function ($query) use ($officer) {
                $query
                    ->where('assigned_to', $officer->id)
                    ->whereNull('completed_at');
            })
            ->first();

        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Complaint does not belong to your ward or its incident is not assigned to you.',
            ], 403);
        }

        $incident = $complaint->incident;

        if (!$incident) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint is not linked to a civic incident.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Validate status transition
        |--------------------------------------------------------------------------
        */

        $allowed = match ($complaint->status) {
            'assigned' => ['in_progress'],
            'in_progress' => ['resolved'],
            default => [],
        };

        if (!in_array($data['status'], $allowed, true)) {
            return response()->json([
                'success' => false,
                'message' =>
                    "Invalid status transition from {$complaint->status} to {$data['status']}.",
            ], 422);
        }

        DB::transaction(function () use (
            $complaint,
            $incident,
            $data,
            $officer
        ) {

            /*
            |--------------------------------------------------------------------------
            | Update Complaint
            |--------------------------------------------------------------------------
            */

            $complaintUpdate = [
                'status' => $data['status'],
            ];

            if ($data['status'] === 'resolved') {
                $complaintUpdate['resolved_at'] = now();
            }

            $complaint->update($complaintUpdate);

            /*
            |--------------------------------------------------------------------------
            | Update Incident
            |--------------------------------------------------------------------------
            */

            if ($data['status'] === 'in_progress') {

                $incident->update([
                    'status' => 'in_progress',
                ]);
            }

            if ($data['status'] === 'resolved') {

                $incident->update([
                    'status' => 'resolved',
                    'resolved_at' => now(),
                ]);

                /*
                |--------------------------------------------------------------------------
                | Complete Incident Assignment
                |--------------------------------------------------------------------------
                */

                $assignment = $incident->assignments
                    ->whereNull('completed_at')
                    ->sortByDesc('id')
                    ->first();

                if ($assignment) {
                    $assignment->update([
                        'completed_at' => now(),
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Complaint Status History
            |--------------------------------------------------------------------------
            */

            $complaint->statusHistory()->create([
                'status' =>
                    $data['status'],

                'changed_by' =>
                    $officer->id,

                'remarks' =>
                    $data['remarks']
                    ?? match ($data['status']) {

                        'in_progress' =>
                            'Field officer started working on the complaint.',

                        'resolved' =>
                            'Field officer marked the complaint as resolved.',

                        default =>
                            'Complaint status updated.',
                    },
            ]);
        });

        $complaint->refresh();
        $incident->refresh();

        return response()->json([
            'success' => true,

            'message' =>
                'Complaint and incident status updated successfully.',

            'complaint' => [
                'id' =>
                    $complaint->id,

                'complaint_number' =>
                    $complaint->complaint_number,

                'status' =>
                    $complaint->status,

                'resolved_at' =>
                    $complaint->resolved_at?->toISOString(),
            ],

            'incident' => [
                'id' =>
                    $incident->id,

                'incident_number' =>
                    $incident->incident_number,

                'status' =>
                    $incident->status,

                'resolved_at' =>
                    $incident->resolved_at?->toISOString(),
            ],
        ]);
    }
}