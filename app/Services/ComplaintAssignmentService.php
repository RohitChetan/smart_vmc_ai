<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\ComplaintAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ComplaintAssignmentService
{
    public function assign(Complaint $complaint): ?ComplaintAssignment
    {
        if (!$complaint->ward_id || !$complaint->department_id) {
            return null;
        }

        $officer = User::where(
            'email',
            'ward11.officer@smartvadodara.test'
        )->first();

        if (!$officer) {
            return null;
        }

        return DB::transaction(function () use (
            $complaint,
            $officer
        ) {

            $assignment = ComplaintAssignment::create([
                'complaint_id' => $complaint->id,
                'assigned_to' => $officer->id,
                'assigned_by' => null,
                'assigned_at' => now(),
                'notes' =>
                    'Automatically assigned by Smart Vadodara AI routing engine.',
            ]);

            $dueHours = match ($complaint->priority) {
                'critical' => 4,
                'high' => 12,
                'medium' => 24,
                'low' => 48,
                default => 24,
            };

            $complaint->update([
                'status' => 'assigned',
                'due_at' => now()->addHours($dueHours),
            ]);

            $complaint->statusHistory()->create([
                'status' => 'assigned',
                'changed_by' => null,
                'remarks' =>
                    "Automatically assigned to {$officer->name}. SLA: {$dueHours} hours.",
            ]);

            return $assignment;
        });
    }
}
