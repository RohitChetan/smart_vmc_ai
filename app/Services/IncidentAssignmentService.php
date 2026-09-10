<?php

namespace App\Services;

use App\Models\CivicIncident;
use App\Models\IncidentAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class IncidentAssignmentService
{
    public function assign(
        CivicIncident $incident,
        ?User $assignedTo = null,
        ?User $assignedBy = null,
        ?string $notes = null
    ): IncidentAssignment {

        return DB::transaction(function () use (
            $incident,
            $assignedTo,
            $assignedBy,
            $notes
        ) {

            /*
            |--------------------------------------------------------------------------
            | Existing Active Assignment
            |--------------------------------------------------------------------------
            */

            $existingAssignment = $incident
                ->assignments()
                ->whereNull('completed_at')
                ->latest('id')
                ->first();

            if ($existingAssignment) {

                /*
                | Existing assignment has no officer:
                | attempt automatic repair.
                */
                if (!$existingAssignment->assigned_to) {

                    $officer = $assignedTo
                        ?? $this->findOfficerForIncident(
                            $incident
                        );

                    if ($officer) {
                        $existingAssignment->update([
                            'assigned_to' =>
                                $officer->id,
                        ]);
                    }
                }

                /*
                | If an officer exists, incident must
                | reflect assigned state.
                */
                if (
                    $existingAssignment
                        ->fresh()
                        ->assigned_to
                ) {
                    $this->markIncidentAssigned(
                        $incident
                    );
                }

                return $existingAssignment->fresh([
                    'assignedTo',
                    'assignedBy',
                    'incident',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Find Ward Officer
            |--------------------------------------------------------------------------
            */

            $assignedTo = $assignedTo
                ?? $this->findOfficerForIncident(
                    $incident
                );

            /*
            |--------------------------------------------------------------------------
            | Create Assignment
            |--------------------------------------------------------------------------
            */

            $assignment =
                IncidentAssignment::create([
                    'incident_id' =>
                        $incident->id,

                    'assigned_to' =>
                        $assignedTo?->id,

                    'assigned_by' =>
                        $assignedBy?->id,

                    'assigned_at' =>
                        now(),

                    'notes' =>
                        $notes
                        ?? 'Automatically assigned by Smart Vadodara incident routing engine.',
                ]);

            /*
            |--------------------------------------------------------------------------
            | Update Incident Status
            |--------------------------------------------------------------------------
            |
            | Only mark assigned when an actual officer exists.
            |
            */

            if ($assignedTo) {
                $this->markIncidentAssigned(
                    $incident
                );
            }

            return $assignment->fresh([
                'assignedTo',
                'assignedBy',
                'incident',
            ]);
        });
    }

    /**
     * Find ward officer dynamically.
     */
    private function findOfficerForIncident(
        CivicIncident $incident
    ): ?User {
        if (!$incident->ward_id) {
            return null;
        }

        return User::query()
            ->where('role', 'ward_officer')
            ->where(
                'ward_id',
                $incident->ward_id
            )
            ->orderBy('id')
            ->first();
    }

    /**
     * Mark incident assigned.
     */
    private function markIncidentAssigned(
        CivicIncident $incident
    ): void {
        /*
        | Do not move active work backwards.
        */
        if (
            in_array(
                $incident->status,
                [
                    'in_progress',
                    'verification_pending',
                    'resolved',
                    'closed',
                ],
                true
            )
        ) {
            return;
        }

        $incident->update([
            'status' => 'assigned',
        ]);
    }

    /**
     * Check active assignment.
     */
    public function hasActiveAssignment(
        CivicIncident $incident
    ): bool {
        return $incident
            ->assignments()
            ->whereNull('completed_at')
            ->exists();
    }

    /**
     * Get active assignment.
     */
    public function activeAssignment(
        CivicIncident $incident
    ): ?IncidentAssignment {
        return $incident
            ->assignments()
            ->whereNull('completed_at')
            ->latest('id')
            ->first();
    }
}