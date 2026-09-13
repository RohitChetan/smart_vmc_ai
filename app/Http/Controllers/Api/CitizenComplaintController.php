<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintStatusHistory;
use App\Services\IncidentAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CivicPoint;
use App\Models\CitizenReward;
use App\Models\Certificate;

class CitizenComplaintController extends Controller
{
    /**
     * List complaints belonging to the authenticated citizen.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $complaints = Complaint::with([
            'userCategory',
            'aiCategory',
            'department',
            'ward',
            'incident',
        ])
            ->where('user_id', $user->id)
            ->latest('submitted_at')
            ->latest('id')
            ->get();

        $total = $complaints->count();

        $activeStatuses = [
            'submitted',
            'ai_processing',
            'assigned',
            'in_progress',
            'verification_pending',
            'reopened',
        ];

        $active = $complaints
            ->whereIn('status', $activeStatuses)
            ->count();

        $resolved = $complaints
            ->whereIn('status', [
                'resolved',
                'closed',
            ])
            ->count();

        return response()->json([
            'success' => true,

            'summary' => [
                'total' => $total,
                'active' => $active,
                'resolved' => $resolved,
            ],

            'complaints' => $complaints->map(function ($complaint) {
                return [
                    'id' => $complaint->id,

                    'complaint_number' =>
                        $complaint->complaint_number,

                    'title' => $complaint->description
                        ? mb_substr(
                            $complaint->description,
                            0,
                            80
                        )
                        : 'Civic Issue',

                    'description' =>
                        $complaint->description,

                    'category' =>
                        $complaint->aiCategory?->name
                        ?? $complaint->userCategory?->name,

                    'department' =>
                        $complaint->department?->name,

                    'ward' => $complaint->ward ? [
                        'id' => $complaint->ward->id,
                        'ward_no' => $complaint->ward->ward_no,
                        'name' => $complaint->ward->name,
                    ] : null,

                    'priority' =>
                        $complaint->priority,

                    'status' =>
                        $complaint->status,

                    'latitude' =>
                        $complaint->latitude,

                    'longitude' =>
                        $complaint->longitude,

                    'submitted_at' =>
                        $complaint->submitted_at,

                    'due_at' =>
                        $complaint->due_at,

                    'resolved_at' =>
                        $complaint->resolved_at,

                    'closed_at' =>
                        $complaint->closed_at,

                    'incident' => $complaint->incident ? [
                        'id' =>
                            $complaint->incident->id,

                        'incident_number' =>
                            $complaint->incident->incident_number,

                        'report_count' =>
                            $complaint->incident->report_count,
                    ] : null,
                ];
            })->values(),
        ]);
    }

    /**
     * Citizen confirms that the reported issue is resolved.
     *
     * verification_pending -> closed
     */
    public function verifyResolved(
        Request $request,
        string $complaintNumber
    ) {
        return DB::transaction(
            function () use ($request, $complaintNumber) {

                $complaint = Complaint::with('incident')
                    ->where(
                        'complaint_number',
                        $complaintNumber
                    )
                    ->where(
                        'user_id',
                        $request->user()->id
                    )
                    ->lockForUpdate()
                    ->first();

                if (!$complaint) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Complaint not found.',
                    ], 404);
                }

                if (
                    $complaint->status !==
                    'verification_pending'
                ) {
                    return response()->json([
                        'success' => false,
                        'message' =>
                            'Complaint is not awaiting citizen verification.',
                        'current_status' =>
                            $complaint->status,
                    ], 422);
                }

                $complaint->update([
                    'status' => 'closed',
                    'closed_at' => now(),
                ]);

                // Award civic points only once for this complaint.
                $civicPoint = CivicPoint::firstOrCreate(
                    [
                        'user_id' => $request->user()->id,
                        'complaint_id' => $complaint->id,
                    ],
                    [
                        'points' => 10,
                        'reason' => 'Citizen confirmed civic issue resolution.',
                    ]
                );

                $reward = CitizenReward::firstOrCreate(
                    [
                        'user_id' => $request->user()->id,
                    ],
                    [
                        'total_points' => 0,
                        'level' => 'Citizen',
                    ]
                );

                if ($civicPoint->wasRecentlyCreated) {
                    $reward->increment('total_points', $civicPoint->points);
                    $reward->refresh();
                }

                // Create Jagruk Nagrik certificate only once.
                $certificate = Certificate::firstOrCreate(
                    [
                        'user_id' => $request->user()->id,
                        'complaint_id' => $complaint->id,
                    ],
                    [
                        'certificate_number' => 'JNC-' . now()->format('Y') . '-' . strtoupper(
                            substr(str()->uuid()->toString(), 0, 8)
                        ),
                        'title' => 'Jagruk Nagrik Certificate',
                        'issued_at' => now(),
                    ]
                );

                ComplaintStatusHistory::create([
                    'complaint_id' =>
                        $complaint->id,

                    'status' =>
                        'closed',

                    'changed_by' =>
                        $request->user()->id,

                    'remarks' =>
                        'Citizen confirmed that the civic issue has been resolved.',
                ]);

                /*
                 * Close the shared incident only when every complaint
                 * attached to it is already closed or rejected.
                 */
                $incident = $complaint->incident;

                if ($incident) {

                    $openComplaintExists =
                        $incident->complaints()
                            ->whereNotIn(
                                'status',
                                [
                                    'closed',
                                    'rejected',
                                ]
                            )
                            ->exists();

                    if (!$openComplaintExists) {

                        $incident->update([
                            'status' =>
                                'closed',

                            'closed_at' =>
                                now(),
                        ]);

                        $activeAssignment =
                            $incident->assignments()
                                ->whereNull(
                                    'completed_at'
                                )
                                ->latest('id')
                                ->first();

                        if ($activeAssignment) {
                            $activeAssignment->update([
                                'completed_at' =>
                                    now(),
                            ]);
                        }
                    }
                }

                return response()->json([
                    'success' => true,

                    'message' =>
                        'Complaint closed after citizen verification.',

                    'complaint' =>
                        $complaint->fresh([
                            'ward',
                            'aiCategory',
                            'department',
                            'incident',
                        ]),
                ]);
            }
        );
    }

    /**
     * Citizen reports that the issue is still not resolved.
     *
     * Supported transitions:
     *
     * verification_pending -> reopened -> assigned
     * closed               -> reopened -> assigned
     */
    public function reopen(
        Request $request,
        string $complaintNumber
    ) {
        return DB::transaction(
            function () use ($request, $complaintNumber) {

                /*
                 * IMPORTANT:
                 * Keep ownership protection.
                 *
                 * A citizen can only reopen their own complaint.
                 */
                $complaint = Complaint::with('incident')
                    ->where(
                        'complaint_number',
                        $complaintNumber
                    )
                    ->where(
                        'user_id',
                        $request->user()->id
                    )
                    ->lockForUpdate()
                    ->first();

                if (!$complaint) {
                    return response()->json([
                        'success' => false,
                        'message' =>
                            'Complaint not found.',
                    ], 404);
                }

                /*
                 * Reopening is allowed from both:
                 *
                 * 1. verification_pending
                 * 2. closed
                 */
                if (
                    !in_array(
                        $complaint->status,
                        [
                            'verification_pending',
                            'closed',
                        ],
                        true
                    )
                ) {
                    return response()->json([
                        'success' => false,

                        'message' =>
                            'Complaint cannot be reopened from its current status.',

                        'current_status' =>
                            $complaint->status,
                    ], 422);
                }

                $remarks = $request->input(
                    'remarks',
                    'Citizen reported that the civic issue is still not resolved.'
                );

                /*
                 * First move complaint to reopened.
                 */
                $complaint->update([
                    'status' =>
                        'reopened',

                    'resolved_at' =>
                        null,

                    'closed_at' =>
                        null,
                ]);

                ComplaintStatusHistory::create([
                    'complaint_id' =>
                        $complaint->id,

                    'status' =>
                        'reopened',

                    'changed_by' =>
                        $request->user()->id,

                    'remarks' =>
                        $remarks,
                ]);

                $incident =
                    $complaint->incident;

                /*
                 * If complaint belongs to an incident,
                 * restore the incident workflow.
                 */
                if ($incident) {

                    $incident->update([
                        'status' =>
                            'reopened',

                        'resolved_at' =>
                            null,

                        'closed_at' =>
                            null,
                    ]);

                    /*
                     * Restore/create active incident assignment.
                     *
                     * IncidentAssignmentService:
                     *
                     * - Reuses an existing active assignment
                     * - If no active assignment exists,
                     *   finds the Ward Officer automatically
                     */
                    $assignmentService =
                        app(
                            IncidentAssignmentService::class
                        );

                    $assignment =
                        $assignmentService->assign(
                            $incident->fresh(),
                            null,
                            null,
                            'Reassigned automatically after citizen reopened the complaint.'
                        );

                    /*
                     * Move incident back into field workflow.
                     */
                    $incident->update([
                        'status' =>
                            'assigned',
                    ]);

                    /*
                     * Move complaint into assigned state so
                     * the field officer dashboard can pick it up.
                     */
                    $complaint->update([
                        'status' =>
                            'assigned',
                    ]);

                    /*
                     * Record the automatic reassignment.
                     */
                    ComplaintStatusHistory::create([
                        'complaint_id' =>
                            $complaint->id,

                        'status' =>
                            'assigned',

                        'changed_by' =>
                            null,

                        'remarks' =>
                            $assignment->assigned_to
                                ? 'Complaint automatically reassigned after citizen reopened the issue.'
                                : 'Complaint reopened and queued for field assignment.',
                    ]);
                }

                /*
                 * Refresh relationships so the API response
                 * contains the latest state.
                 */
                $complaint->refresh();

                $complaint->load([
                    'ward',
                    'aiCategory',
                    'department',
                    'incident',
                ]);

                if ($incident) {
                    $incident->refresh();

                    $incident->load([
                        'category',
                        'department',
                        'ward',
                        'assignments.assignedTo',
                    ]);
                }

                return response()->json([
                    'success' => true,

                    'message' => $incident
                        ? 'Complaint reopened and reassigned for further field action.'
                        : 'Complaint reopened. Further field action is required.',

                    'complaint' =>
                        $complaint,

                    'incident' =>
                        $incident,
                ]);
            }
        );
    }
}