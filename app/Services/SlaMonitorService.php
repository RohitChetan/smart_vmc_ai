<?php

namespace App\Services;

use App\Models\CivicIncident;
use App\Models\IncidentEscalation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SlaMonitorService
{
    /**
     * Check all active incidents and create
     * SLA escalation records where required.
     */
    public function monitor(): array
    {
        $now = now();

        /*
        |--------------------------------------------------------------------------
        | Resolve stale SLA escalations
        |--------------------------------------------------------------------------
        |
        | Once an incident reaches verification_pending,
        | resolved, or closed state, it should no longer
        | have an active SLA escalation.
        |
        | This also cleans up old/stale escalation records
        | created before the incident status changed.
        |
        */
        $this->resolveInactiveIncidentEscalations();

        $incidents = CivicIncident::query()
            ->whereNotNull('due_at')
            ->whereIn('status', [
                'open',
                'assigned',
                'in_progress',
                'reopened',
            ])
            ->get();

        $summary = [
            'checked' => 0,
            'warning' => 0,
            'breached' => 0,
            'critical' => 0,
            'resolved' => 0,
        ];

        foreach ($incidents as $incident) {

            $summary['checked']++;

            $level = $this->determineLevel(
                $incident,
                $now
            );

            if (!$level) {
                continue;
            }

            $created = $this->createEscalationIfNeeded(
                $incident,
                $level,
                $now
            );

            if ($created) {
                $summary[$level]++;
            }
        }

        return $summary;
    }

    /**
     * Resolve active escalations for incidents
     * that are no longer SLA-monitored.
     */
    private function resolveInactiveIncidentEscalations(): int
    {
        return IncidentEscalation::query()
            ->whereNull('resolved_at')
            ->whereHas('incident', function ($query) {
                $query->whereIn('status', [
                    'verification_pending',
                    'resolved',
                    'closed',
                ]);
            })
            ->update([
                'resolved_at' => now(),
            ]);
    }

    /**
     * Determine current SLA escalation level.
     */
    private function determineLevel(
        CivicIncident $incident,
        Carbon $now
    ): ?string {

        if (!$incident->due_at) {
            return null;
        }

        $dueAt = $incident->due_at;

        /*
         * Critical incidents get immediate
         * critical escalation once overdue.
         */
        if (
            $incident->priority === 'critical'
            && $now->greaterThanOrEqualTo($dueAt)
        ) {
            return 'critical';
        }

        /*
         * Any incident that is overdue
         * is SLA breached.
         */
        if ($now->greaterThanOrEqualTo($dueAt)) {
            return 'breached';
        }

        /*
         * Warning when 75% or more of the SLA
         * window has elapsed.
         */
        $start = $this->getSlaStartTime($incident);

        if (!$start) {
            return null;
        }

        $totalSeconds = $start->diffInSeconds($dueAt);

        if ($totalSeconds <= 0) {
            return null;
        }

        $elapsedSeconds = $start->diffInSeconds($now);

        $percentage = (
            $elapsedSeconds / $totalSeconds
        ) * 100;

        if ($percentage >= 75) {
            return 'warning';
        }

        return null;
    }

    /**
     * Determine the SLA start time.
     */
    private function getSlaStartTime(
        CivicIncident $incident
    ): ?Carbon {

        return $incident->first_reported_at
            ?? $incident->created_at;
    }

    /**
     * Create an escalation only once for the
     * same incident + level while active.
     */
    private function createEscalationIfNeeded(
        CivicIncident $incident,
        string $level,
        Carbon $now
    ): bool {

        return DB::transaction(
            function () use (
                $incident,
                $level,
                $now
            ) {

                $existing = IncidentEscalation::query()
                    ->where('incident_id', $incident->id)
                    ->where('level', $level)
                    ->whereNull('resolved_at')
                    ->exists();

                if ($existing) {
                    return false;
                }

                $reason = match ($level) {

                    'warning' =>
                        'Incident has reached 75% of its SLA window.',

                    'breached' =>
                        'Incident has exceeded its SLA deadline.',

                    'critical' =>
                        'Critical incident has exceeded its SLA deadline.',

                    default =>
                        'SLA escalation triggered.',
                };

                IncidentEscalation::create([
                    'incident_id' => $incident->id,
                    'level' => $level,
                    'reason' => $reason,
                    'triggered_at' => $now,
                ]);

                return true;
            }
        );
    }

    /**
     * Mark active escalations resolved when
     * an incident is completed.
     */
    public function resolveForIncident(
        CivicIncident $incident
    ): int {

        return IncidentEscalation::query()
            ->where('incident_id', $incident->id)
            ->whereNull('resolved_at')
            ->update([
                'resolved_at' => now(),
            ]);
    }
}