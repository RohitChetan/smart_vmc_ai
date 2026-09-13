<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CivicIncident;
use App\Models\Complaint;
use App\Models\Department;
use App\Models\IncidentEscalation;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminDashboardController extends Controller
{
    /**
     * Admin Command Center dashboard.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $incidents = CivicIncident::query();
        $complaints = Complaint::query();

        return response()->json([
            'success' => true,

            'summary' => [
                'total_incidents' => (clone $incidents)->count(),
                'open_incidents' => (clone $incidents)->where('status', 'open')->count(),
                'assigned_incidents' => (clone $incidents)->where('status', 'assigned')->count(),
                'in_progress_incidents' => (clone $incidents)->where('status', 'in_progress')->count(),
                'verification_pending_incidents' => (clone $incidents)
                    ->where('status', 'verification_pending')
                    ->count(),
                'resolved_incidents' => (clone $incidents)->where('status', 'resolved')->count(),
                'closed_incidents' => (clone $incidents)->where('status', 'closed')->count(),
                'reopened_incidents' => (clone $incidents)->where('status', 'reopened')->count(),
                'total_complaints' => (clone $complaints)->count(),
            ],

            'priority' => [
                'critical' => (clone $incidents)->where('priority', 'critical')->count(),
                'high' => (clone $incidents)->where('priority', 'high')->count(),
                'medium' => (clone $incidents)->where('priority', 'medium')->count(),
                'low' => (clone $incidents)->where('priority', 'low')->count(),
            ],

            'sla' => [
                'warning' => IncidentEscalation::query()
                    ->where('level', 'warning')
                    ->whereNull('resolved_at')
                    ->count(),

                'breached' => IncidentEscalation::query()
                    ->where('level', 'breached')
                    ->whereNull('resolved_at')
                    ->count(),

                'critical' => IncidentEscalation::query()
                    ->where('level', 'critical')
                    ->whereNull('resolved_at')
                    ->count(),
            ],

            'wards' => Ward::query()
                ->withCount([
                    'incidents',
                    'complaints',
                ])
                ->orderBy('ward_no')
                ->get()
                ->map(function ($ward) {
                    return [
                        'ward_id' => $ward->id,
                        'ward_no' => $ward->ward_no,
                        'name' => $ward->name,
                        'incident_count' => $ward->incidents_count,
                        'complaint_count' => $ward->complaints_count,
                    ];
                })
                ->values(),

            'departments' => Department::query()
                ->withCount('incidents')
                ->orderBy('name')
                ->get()
                ->map(function ($department) {
                    return [
                        'department_id' => $department->id,
                        'name' => $department->name,
                        'code' => $department->code,
                        'incident_count' => $department->incidents_count,
                    ];
                })
                ->values(),

            'officers' => User::query()
                ->where('role', 'ward_officer')
                ->with('ward')
                ->withCount([
                    'incidentAssignments as active_assignments_count' => function ($query) {
                        $query->whereNull('completed_at');
                    },
                ])
                ->orderBy('id')
                ->get()
                ->map(function ($officer) {
                    return [
                        'officer_id' => $officer->id,
                        'name' => $officer->name,
                        'email' => $officer->email,

                        'ward' => $officer->ward ? [
                            'id' => $officer->ward->id,
                            'ward_no' => $officer->ward->ward_no,
                            'name' => $officer->ward->name,
                        ] : null,

                        'active_assignments' => $officer->active_assignments_count,
                    ];
                })
                ->values(),

            'generated_at' => now()->toISOString(),
        ]);
    }

    /**
     * Admin incident list.
     */
    public function incidents(Request $request): JsonResponse
    {
        $query = CivicIncident::query()
            ->with([
                'category',
                'department',
                'ward',
                'assignments.assignedTo',
                'escalations' => function ($query) {
                    $query->whereNull('resolved_at')
                        ->latest('id');
                },
            ])
            ->withCount('complaints');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->string('priority'));
        }

        if ($request->filled('ward_id')) {
            $query->where('ward_id', $request->integer('ward_id'));
        }

        if ($request->filled('department_id')) {
            $query->where(
                'department_id',
                $request->integer('department_id')
            );
        }

        $incidents = $query
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
            ->orderByDesc('id')
            ->paginate(
                $request->integer('per_page', 25)
            );

        return response()->json([
            'success' => true,

            'data' => $incidents->getCollection()
                ->map(function ($incident) {

                    $activeAssignment = $incident->assignments
                        ->whereNull('completed_at')
                        ->sortByDesc('id')
                        ->first();

                    $activeEscalation = $incident->escalations
                        ->whereNull('resolved_at')
                        ->sortByDesc('id')
                        ->first();

                    return [
                        'id' => $incident->id,
                        'incident_number' => $incident->incident_number,

                        'category' => $incident->category ? [
                            'id' => $incident->category->id,
                            'name' => $incident->category->name,
                        ] : null,

                        'department' => $incident->department ? [
                            'id' => $incident->department->id,
                            'name' => $incident->department->name,
                            'code' => $incident->department->code,
                        ] : null,

                        'ward' => $incident->ward ? [
                            'id' => $incident->ward->id,
                            'ward_no' => $incident->ward->ward_no,
                            'name' => $incident->ward->name,
                        ] : null,

                        'priority' => $incident->priority,
                        'status' => $incident->status,

                        'report_count' => $incident->report_count,
                        'complaint_count' => $incident->complaints_count,

                        'assigned_officer' => $activeAssignment?->assignedTo ? [
                            'id' => $activeAssignment->assignedTo->id,
                            'name' => $activeAssignment->assignedTo->name,
                            'email' => $activeAssignment->assignedTo->email,
                        ] : null,

                        'sla' => [
                            'due_at' => $incident->due_at?->toISOString(),

                            'escalation' => $activeEscalation ? [
                                'id' => $activeEscalation->id,
                                'level' => $activeEscalation->level,
                                'reason' => $activeEscalation->reason,
                                'triggered_at' =>
                                    $activeEscalation->triggered_at
                                        ?->toISOString(),
                            ] : null,
                        ],

                        'location' => [
                            'latitude' => $incident->latitude,
                            'longitude' => $incident->longitude,
                        ],

                        'reported_at' =>
                            $incident->first_reported_at?->toISOString(),

                        'last_reported_at' =>
                            $incident->last_reported_at?->toISOString(),

                        'resolved_at' =>
                            $incident->resolved_at?->toISOString(),

                        'closed_at' =>
                            $incident->closed_at?->toISOString(),

                        'created_at' =>
                            $incident->created_at?->toISOString(),
                    ];
                })
                ->values(),

            'pagination' => [
                'current_page' => $incidents->currentPage(),
                'last_page' => $incidents->lastPage(),
                'per_page' => $incidents->perPage(),
                'total' => $incidents->total(),
            ],
        ]);
    }

    /**
     * Admin SLA monitoring details.
     */
    public function sla(Request $request): JsonResponse
    {
        $query = IncidentEscalation::query()
            ->with([
                'incident.category',
                'incident.department',
                'incident.ward',
                'incident.assignments.assignedTo',
            ])
            ->whereNull('resolved_at');

        if ($request->filled('level')) {
            $query->where('level', $request->string('level'));
        }

        if ($request->filled('ward_id')) {
            $query->whereHas('incident', function ($incidentQuery) use ($request) {
                $incidentQuery->where(
                    'ward_id',
                    $request->integer('ward_id')
                );
            });
        }

        $escalations = $query
            ->orderByRaw("
                CASE level
                    WHEN 'critical' THEN 1
                    WHEN 'breached' THEN 2
                    WHEN 'warning' THEN 3
                    ELSE 4
                END
            ")
            ->orderByDesc('triggered_at')
            ->paginate(25);

        return response()->json([
            'success' => true,

            'summary' => [
                'warning' => IncidentEscalation::query()
                    ->where('level', 'warning')
                    ->whereNull('resolved_at')
                    ->count(),

                'breached' => IncidentEscalation::query()
                    ->where('level', 'breached')
                    ->whereNull('resolved_at')
                    ->count(),

                'critical' => IncidentEscalation::query()
                    ->where('level', 'critical')
                    ->whereNull('resolved_at')
                    ->count(),

                'total_active' => IncidentEscalation::query()
                    ->whereNull('resolved_at')
                    ->count(),
            ],

            'data' => $escalations->getCollection()
                ->map(function ($escalation) {

                    $incident = $escalation->incident;

                    $activeAssignment = $incident?->assignments
                        ->whereNull('completed_at')
                        ->sortByDesc('id')
                        ->first();

                    return [
                        'escalation_id' => $escalation->id,
                        'level' => $escalation->level,
                        'reason' => $escalation->reason,

                        'triggered_at' =>
                            $escalation->triggered_at?->toISOString(),

                        'incident' => $incident ? [
                            'id' => $incident->id,
                            'incident_number' =>
                                $incident->incident_number,

                            'category' => $incident->category ? [
                                'id' => $incident->category->id,
                                'name' => $incident->category->name,
                            ] : null,

                            'department' => $incident->department ? [
                                'id' => $incident->department->id,
                                'name' => $incident->department->name,
                                'code' => $incident->department->code,
                            ] : null,

                            'ward' => $incident->ward ? [
                                'id' => $incident->ward->id,
                                'ward_no' => $incident->ward->ward_no,
                                'name' => $incident->ward->name,
                            ] : null,

                            'priority' => $incident->priority,
                            'status' => $incident->status,
                            'due_at' => $incident->due_at?->toISOString(),

                            'assigned_officer' =>
                                $activeAssignment?->assignedTo ? [
                                    'id' =>
                                        $activeAssignment->assignedTo->id,
                                    'name' =>
                                        $activeAssignment->assignedTo->name,
                                ] : null,

                            'location' => [
                                'latitude' => $incident->latitude,
                                'longitude' => $incident->longitude,
                            ],
                        ] : null,
                    ];
                })
                ->values(),

            'pagination' => [
                'current_page' => $escalations->currentPage(),
                'last_page' => $escalations->lastPage(),
                'per_page' => $escalations->perPage(),
                'total' => $escalations->total(),
            ],
        ]);
    }

    /**
     * Admin live incident map data.
     */
    public function map(Request $request): JsonResponse
    {
        $query = CivicIncident::query()
            ->with([
                'category',
                'department',
                'ward',
                'escalations' => function ($query) {
                    $query->whereNull('resolved_at')
                        ->latest('id');
                },
            ]);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->string('priority'));
        }

        if ($request->filled('ward_id')) {
            $query->where('ward_id', $request->integer('ward_id'));
        }

        if ($request->filled('department_id')) {
            $query->where(
                'department_id',
                $request->integer('department_id')
            );
        }

        $query->whereNotNull('latitude')
            ->whereNotNull('longitude');

        $incidents = $query
            ->orderByRaw("
                CASE priority
                    WHEN 'critical' THEN 1
                    WHEN 'high' THEN 2
                    WHEN 'medium' THEN 3
                    WHEN 'low' THEN 4
                    ELSE 5
                END
            ")
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $incidents->count(),

            'data' => $incidents->map(function ($incident) {

                $activeEscalation = $incident->escalations
                    ->whereNull('resolved_at')
                    ->sortByDesc('id')
                    ->first();

                return [
                    'id' => $incident->id,
                    'incident_number' =>
                        $incident->incident_number,

                    'category' => $incident->category ? [
                        'id' => $incident->category->id,
                        'name' => $incident->category->name,
                    ] : null,

                    'department' => $incident->department ? [
                        'id' => $incident->department->id,
                        'name' => $incident->department->name,
                        'code' => $incident->department->code,
                    ] : null,

                    'ward' => $incident->ward ? [
                        'id' => $incident->ward->id,
                        'ward_no' => $incident->ward->ward_no,
                        'name' => $incident->ward->name,
                    ] : null,

                    'priority' => $incident->priority,
                    'status' => $incident->status,
                    'report_count' => $incident->report_count,

                    'sla' => [
                        'due_at' =>
                            $incident->due_at?->toISOString(),
                        'level' =>
                            $activeEscalation?->level,
                    ],

                    'location' => [
                        'latitude' => (float) $incident->latitude,
                        'longitude' => (float) $incident->longitude,
                    ],

                    'reported_at' =>
                        $incident->first_reported_at?->toISOString(),

                    'updated_at' =>
                        $incident->updated_at?->toISOString(),
                ];
            })->values(),

            'generated_at' => now()->toISOString(),
        ]);
    }

    /**
     * Admin incident detail.
     */
    public function incidentDetail(CivicIncident $incident): JsonResponse
    {
        $incident->load([
            'category',
            'department',
            'ward',

            'assignments.assignedTo',

            'escalations' => function ($query) {
                $query->latest('id');
            },

            'complaints.userCategory',
            'complaints.aiCategory',
            'complaints.department',
            'complaints.ward',
            'complaints.media',
            'complaints.aiAnalyses',
            'complaints.resolutionProofs',
            'complaints.statusHistory',
        ]);

        $activeAssignment = $incident->assignments
            ->whereNull('completed_at')
            ->sortByDesc('id')
            ->first();

        return response()->json([
            'success' => true,

            'incident' => [
                'id' => $incident->id,
                'incident_number' => $incident->incident_number,

                'title' => $incident->title,
                'description' => $incident->description,

                'category' => $incident->category ? [
                    'id' => $incident->category->id,
                    'name' => $incident->category->name,
                ] : null,

                'department' => $incident->department ? [
                    'id' => $incident->department->id,
                    'name' => $incident->department->name,
                    'code' => $incident->department->code,
                ] : null,

                'ward' => $incident->ward ? [
                    'id' => $incident->ward->id,
                    'ward_no' => $incident->ward->ward_no,
                    'name' => $incident->ward->name,
                ] : null,

                'priority' => $incident->priority,
                'status' => $incident->status,
                'report_count' => $incident->report_count,

                'location' => [
                    'latitude' => $incident->latitude,
                    'longitude' => $incident->longitude,
                    'accuracy' => $incident->location_accuracy,
                ],

                'sla' => [
                    'due_at' => $incident->due_at?->toISOString(),
                    'resolved_at' =>
                        $incident->resolved_at?->toISOString(),
                    'closed_at' =>
                        $incident->closed_at?->toISOString(),

                    'escalations' => $incident->escalations
                        ->map(function ($escalation) {
                            return [
                                'id' => $escalation->id,
                                'level' => $escalation->level,
                                'reason' => $escalation->reason,
                                'triggered_at' =>
                                    $escalation->triggered_at
                                        ?->toISOString(),
                                'resolved_at' =>
                                    $escalation->resolved_at
                                        ?->toISOString(),
                            ];
                        })
                        ->values(),
                ],

                'assigned_officer' =>
                    $activeAssignment?->assignedTo ? [
                        'id' =>
                            $activeAssignment->assignedTo->id,
                        'name' =>
                            $activeAssignment->assignedTo->name,
                        'email' =>
                            $activeAssignment->assignedTo->email,
                    ] : null,

                'complaints' => $incident->complaints
                    ->sortByDesc('id')
                    ->map(function ($complaint) {

                        return [
                            'id' => $complaint->id,
                            'complaint_number' =>
                                $complaint->complaint_number,

                            'description' =>
                                $complaint->description,

                            'status' =>
                                $complaint->status,

                            'priority' =>
                                $complaint->priority,

                            'user_category' =>
                                $complaint->userCategory ? [
                                    'id' =>
                                        $complaint->userCategory->id,
                                    'name' =>
                                        $complaint->userCategory->name,
                                ] : null,

                            'ai_category' =>
                                $complaint->aiCategory ? [
                                    'id' =>
                                        $complaint->aiCategory->id,
                                    'name' =>
                                        $complaint->aiCategory->name,
                                ] : null,

                            'department' =>
                                $complaint->department ? [
                                    'id' =>
                                        $complaint->department->id,
                                    'name' =>
                                        $complaint->department->name,
                                ] : null,

                            'ward' =>
                                $complaint->ward ? [
                                    'id' =>
                                        $complaint->ward->id,
                                    'ward_no' =>
                                        $complaint->ward->ward_no,
                                    'name' =>
                                        $complaint->ward->name,
                                ] : null,

                            'ai_confidence' =>
                                $complaint->ai_confidence,

                            'ai_decision_reason' =>
                                $complaint->ai_decision_reason,

                            /*
                             * Citizen uploaded media
                             */
                            'media' => $complaint->media
                                ->map(function ($media) {

                                    return [
                                        'id' => $media->id,
                                        'type' => $media->type,
                                        'file_path' =>
                                            $media->file_path,
                                        'url' =>
                                            Storage::disk('public')
                                                ->url(
                                                    $media->file_path
                                                ),
                                        'mime_type' =>
                                            $media->mime_type,
                                        'file_size' =>
                                            $media->file_size,
                                    ];
                                })
                                ->values(),

                            /*
                             * AI analysis
                             */
                            'ai_analyses' => $complaint->aiAnalyses
                                ->sortByDesc('id')
                                ->map(function ($analysis) {

                                    return [
                                        'id' => $analysis->id,

                                        'model_name' =>
                                            $analysis->model_name,

                                        'model_version' =>
                                            $analysis->model_version,

                                        'predicted_category' =>
                                            $analysis->predicted_category,

                                        'confidence' =>
                                            $analysis->confidence,

                                        'detections' =>
                                            $analysis->detections,

                                        'raw_result' =>
                                            $analysis->raw_result,

                                        'status' =>
                                            $analysis->status,

                                        'error_message' =>
                                            $analysis->error_message,
                                    ];
                                })
                                ->values(),

                            /*
                             * Field resolution proof
                             */
                            'resolution_proofs' =>
                                $complaint->resolutionProofs
                                    ->sortByDesc('id')
                                    ->map(function ($proof) {

                                        return [
                                            'id' => $proof->id,

                                            'type' =>
                                                $proof->type,

                                            'file_path' =>
                                                $proof->file_path,

                                            'url' =>
                                                Storage::disk('public')
                                                    ->url(
                                                        $proof->file_path
                                                    ),

                                            'mime_type' =>
                                                $proof->mime_type,

                                            'file_size' =>
                                                $proof->file_size,

                                            'ai_status' =>
                                                $proof->ai_status,

                                            'ai_confidence' =>
                                                $proof->ai_confidence,

                                            'ai_result' =>
                                                $proof->ai_result,

                                            'latitude' =>
                                                $proof->latitude,

                                            'longitude' =>
                                                $proof->longitude,

                                            'location_accuracy' =>
                                                $proof->location_accuracy,

                                            'captured_at' =>
                                                $proof->captured_at
                                                    ?->toISOString(),

                                            'remarks' =>
                                                $proof->remarks,

                                            'verified_at' =>
                                                $proof->verified_at
                                                    ?->toISOString(),
                                        ];
                                    })
                                    ->values(),

                            /*
                             * Complaint status timeline
                             */
                            'status_history' =>
                                $complaint->statusHistory
                                    ->sortBy('created_at')
                                    ->map(function ($history) {

                                        return [
                                            'id' => $history->id,
                                            'status' =>
                                                $history->status,
                                            'remarks' =>
                                                $history->remarks,
                                            'changed_by' =>
                                                $history->changed_by,
                                            'changed_at' =>
                                                $history->created_at
                                                    ?->toISOString(),
                                        ];
                                    })
                                    ->values(),
                        ];
                    })
                    ->values(),

                'reported_at' =>
                    $incident->first_reported_at?->toISOString(),

                'last_reported_at' =>
                    $incident->last_reported_at?->toISOString(),

                'created_at' =>
                    $incident->created_at?->toISOString(),
            ],
        ]);
    }
}