<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class CitizenComplaintController extends Controller
{
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
                    'complaint_number' => $complaint->complaint_number,

                    'title' => $complaint->description
                        ? mb_substr($complaint->description, 0, 80)
                        : 'Civic Issue',

                    'description' => $complaint->description,

                    'category' => $complaint->aiCategory?->name
                        ?? $complaint->userCategory?->name,

                    'department' => $complaint->department?->name,

                    'ward' => $complaint->ward ? [
                        'id' => $complaint->ward->id,
                        'ward_no' => $complaint->ward->ward_no,
                        'name' => $complaint->ward->name,
                    ] : null,

                    'priority' => $complaint->priority,

                    'status' => $complaint->status,

                    'latitude' => $complaint->latitude,
                    'longitude' => $complaint->longitude,

                    'submitted_at' => $complaint->submitted_at,

                    'due_at' => $complaint->due_at,

                    'resolved_at' => $complaint->resolved_at,

                    'closed_at' => $complaint->closed_at,

                    'incident' => $complaint->incident ? [
                        'id' => $complaint->incident->id,
                        'incident_number' => $complaint->incident->incident_number,
                        'report_count' => $complaint->incident->report_count,
                    ] : null,
                ];
            })->values(),
        ]);
    }
}
