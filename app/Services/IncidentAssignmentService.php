<?php

namespace App\Services;

use App\Models\CivicIncident;
use App\Models\IncidentAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class IncidentAssignmentService
{
    /**
     * Assign an incident to a ward officer.
     *
     * Flow:
     * 1. Check existing active assignment.
     * 2. Repair assignment if officer is missing.
     * 3. Find ward officer automatically when required.
     * 4. Create assignment if none exists.
     * 5. Keep incident status synchronized.
     * 6. Automatically calculate SLA due_at.
     */
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
            | Refresh Incident
            |--------------------------------------------------------------------------
            |
            | Make sure we are working with the latest database state.
            |
            */
            $incident->refresh();

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
                |--------------------------------------------------------------------------
                | Repair Existing Assignment
                |--------------------------------------------------------------------------
                |
                | If an active assignment exists but no officer is attached,
                | attempt automatic officer assignment.
                |
                */

                if (!$existingAssignment->assigned_to) {

                    $officer = $assignedTo
                        ?? $this->findOfficerForIncident($incident);

                    if ($officer) {
                        $existingAssignment->update([
                            'assigned_to' => $officer->id,
                        ]);

                        /*
                        | Keep local variable synchronized.
                        */
                        $assignedTo = $officer;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Sync Incident State
                |--------------------------------------------------------------------------
                |
                | If an officer exists, make sure the incident is in assigned
                | state and has an SLA deadline.
                |
                */

                $existingAssignment->refresh();

                if ($existingAssignment->assigned_to) {
                    $this->markIncidentAssigned($incident);
                }

                return $existingAssignment->fresh([
                    'assignedTo',
                    'assignedBy',
                    'incident',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Find Ward Officer Automatically
            |--------------------------------------------------------------------------
            */

            $assignedTo = $assignedTo
                ?? $this->findOfficerForIncident($incident);

            /*
            |--------------------------------------------------------------------------
            | Create Assignment
            |--------------------------------------------------------------------------
            */

            $assignment = IncidentAssignment::create([
                'incident_id' => $incident->id,

                'assigned_to' => $assignedTo?->id,

                'assigned_by' => $assignedBy?->id,

                'assigned_at' => now(),

                'notes' => $notes
                    ?? 'Automatically assigned by Smart Vadodara incident routing engine.',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Update Incident Status + SLA
            |--------------------------------------------------------------------------
            |
            | Only mark the incident as assigned when an actual officer
            | exists.
            |
            */

            if ($assignedTo) {
                $this->markIncidentAssigned($incident);
            }

            return $assignment->fresh([
                'assignedTo',
                'assignedBy',
                'incident',
            ]);
        });
    }

    /**
     * Find a ward officer dynamically.
     *
     * The officer must:
     * - Have ward_officer role
     * - Belong to the incident's ward
     */
    private function findOfficerForIncident(
        CivicIncident $incident
    ): ?User {
        if (!$incident->ward_id) {
            return null;
        }

        return User::query()
            ->where('role', 'ward_officer')
            ->where('ward_id', $incident->ward_id)
            ->orderBy('id')
            ->first();
    }

    /**
     * Mark incident as assigned and ensure SLA exists.
     *
     * Important:
     * Existing active work must never be moved backwards.
     */
    private function markIncidentAssigned(
        CivicIncident $incident
    ): void {
        $incident->refresh();

        /*
        |--------------------------------------------------------------------------
        | Do Not Move Active Work Backwards
        |--------------------------------------------------------------------------
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

        /*
        |--------------------------------------------------------------------------
        | Assigned State + SLA
        |--------------------------------------------------------------------------
        */

        $incident->update([
            'status' => 'assigned',

            /*
            | Preserve existing SLA if already present.
            | Otherwise calculate a fresh SLA based on priority.
            */
            'due_at' => $incident->due_at
                ?? $this->calculateDueAt($incident->priority),
        ]);
    }

    /**
     * Calculate SLA deadline based on incident priority.
     */
    private function calculateDueAt(string $priority)
    {
        return match ($priority) {
            'critical' => now()->addHours(2),

            'high' => now()->addHours(12),

            'medium' => now()->addHours(24),

            'low' => now()->addHours(48),

            default => now()->addHours(24),
        };
    }

    /**
     * Check whether an incident has an active assignment.
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
     * Get the latest active assignment.
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