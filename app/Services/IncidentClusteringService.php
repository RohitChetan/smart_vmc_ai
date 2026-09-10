<?php

namespace App\Services;

use App\Models\CivicIncident;
use App\Models\Complaint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class IncidentClusteringService
{
    /**
     * Complaints within 100 meters can belong
     * to the same civic incident.
     */
    private const MAX_DISTANCE_METERS = 100;

    /**
     * Only recent incidents are considered for clustering.
     */
    private const TIME_WINDOW_HOURS = 72;

    /**
     * Attach complaint to matching incident
     * or create a new incident.
     */
    public function attachComplaint(Complaint $complaint): CivicIncident
    {
        $incident = $this->findMatchingIncident($complaint);

        if ($incident) {
            return $this->attachToExistingIncident(
                $complaint,
                $incident
            );
        }

        return $this->createIncident($complaint);
    }

    /**
     * Find nearest matching active incident.
     */
    private function findMatchingIncident(
        Complaint $complaint
    ): ?CivicIncident {
        $since = Carbon::now()
            ->subHours(self::TIME_WINDOW_HOURS);

        $incidents = CivicIncident::query()
            ->where('ward_id', $complaint->ward_id)
            ->whereIn('status', [
                'open',
                'assigned',
                'in_progress',
                'reopened',
            ])
            ->where('last_reported_at', '>=', $since)
            ->when(
                $complaint->ai_category_id,
                function ($query) use ($complaint) {
                    $query->where(
                        'category_id',
                        $complaint->ai_category_id
                    );
                }
            )
            ->get();

        $bestIncident = null;
        $bestDistance = null;

        foreach ($incidents as $incident) {

            $distance = $this->distanceInMeters(
                (float) $complaint->latitude,
                (float) $complaint->longitude,
                (float) $incident->latitude,
                (float) $incident->longitude
            );

            if (
                $distance <= self::MAX_DISTANCE_METERS &&
                (
                    $bestDistance === null ||
                    $distance < $bestDistance
                )
            ) {
                $bestIncident = $incident;
                $bestDistance = $distance;
            }
        }

        return $bestIncident;
    }

    /**
     * Attach complaint to existing incident.
     */
    private function attachToExistingIncident(
        Complaint $complaint,
        CivicIncident $incident
    ): CivicIncident {

        $complaint->update([
            'incident_id' => $incident->id,
        ]);

        $incident->increment('report_count');

        $incident->refresh();

        /*
        |--------------------------------------------------------------------------
        | Priority
        |--------------------------------------------------------------------------
        |
        | Priority considers BOTH:
        |
        | 1. Highest complaint severity
        | 2. Number of citizen reports
        |
        */

        $priority = $this->calculatePriority(
            $incident,
            $complaint
        );

        $incident->update([
            'last_reported_at' => now(),

            'priority' => $priority,

            /*
            | If priority becomes more severe,
            | recalculate SLA from now.
            */
            'due_at' => $this->calculateDueAt($priority),
        ]);

        return $incident->fresh();
    }

    /**
     * Create brand-new civic incident.
     */
    private function createIncident(
        Complaint $complaint
    ): CivicIncident {
        $now = now();

        /*
        |--------------------------------------------------------------------------
        | Initial Priority
        |--------------------------------------------------------------------------
        |
        | First report should respect AI/complaint priority.
        |
        */

        $priority = $this->normalizePriority(
            $complaint->priority
        );

        $incident = CivicIncident::create([
            'incident_number' =>
                $this->generateIncidentNumber(),

            'category_id' =>
                $complaint->ai_category_id
                ?? $complaint->user_category_id,

            'department_id' =>
                $complaint->department_id,

            'ward_id' =>
                $complaint->ward_id,

            'latitude' =>
                $complaint->latitude,

            'longitude' =>
                $complaint->longitude,

            'location_accuracy' =>
                $complaint->location_accuracy,

            'title' =>
                $this->generateTitle($complaint),

            'description' =>
                $complaint->description,

            'priority' =>
                $priority,

            'status' =>
                'open',

            'report_count' =>
                1,

            'duplicate_confidence' =>
                null,

            'first_reported_at' =>
                $now,

            'last_reported_at' =>
                $now,

            'due_at' =>
                $this->calculateDueAt($priority),
        ]);

        $complaint->update([
            'incident_id' => $incident->id,
        ]);

        return $incident;
    }

    /**
     * Calculate incident priority.
     *
     * Combines:
     * - existing incident severity
     * - new complaint severity
     * - report count escalation
     */
    private function calculatePriority(
        CivicIncident $incident,
        Complaint $complaint
    ): string {

        $existingPriority = $this->normalizePriority(
            $incident->priority
        );

        $complaintPriority = $this->normalizePriority(
            $complaint->priority
        );

        $reportPriority = $this->priorityFromReportCount(
            $incident->report_count
        );

        return $this->highestPriority([
            $existingPriority,
            $complaintPriority,
            $reportPriority,
        ]);
    }

    /**
     * Escalation based on citizen report count.
     */
    private function priorityFromReportCount(
        int $reportCount
    ): string {
        return match (true) {

            $reportCount >= 20 =>
                'critical',

            $reportCount >= 5 =>
                'high',

            $reportCount >= 2 =>
                'medium',

            default =>
                'low',
        };
    }

    /**
     * Return highest priority from supplied priorities.
     */
    private function highestPriority(
        array $priorities
    ): string {
        $weights = [
            'low' => 1,
            'medium' => 2,
            'high' => 3,
            'critical' => 4,
        ];

        $highest = 'low';

        foreach ($priorities as $priority) {

            $priority = $this->normalizePriority(
                $priority
            );

            if (
                $weights[$priority] >
                $weights[$highest]
            ) {
                $highest = $priority;
            }
        }

        return $highest;
    }

    /**
     * Normalize priority.
     */
    private function normalizePriority(
        ?string $priority
    ): string {
        return in_array(
            $priority,
            [
                'low',
                'medium',
                'high',
                'critical',
            ],
            true
        )
            ? $priority
            : 'medium';
    }

    /**
     * Calculate SLA deadline.
     *
     * Temporary MVP SLA policy.
     *
     * Later this can move to database/config
     * and become department/category specific.
     */
    private function calculateDueAt(
        string $priority
    ): Carbon {
        return match ($priority) {

            'critical' =>
                now()->addHours(2),

            'high' =>
                now()->addHours(12),

            'medium' =>
                now()->addHours(24),

            'low' =>
                now()->addHours(48),

            default =>
                now()->addHours(24),
        };
    }

    /**
     * Generate readable incident title.
     */
    private function generateTitle(
        Complaint $complaint
    ): string {
        $category =
            $complaint->aiCategory?->name
            ?? $complaint->userCategory?->name
            ?? 'Civic Issue';

        return $category
            . ' - Ward '
            . ($complaint->ward?->ward_no ?? 'Unknown');
    }

    /**
     * Generate unique incident number.
     */
    private function generateIncidentNumber(): string
    {
        do {
            $number =
                'INC-'
                . now()->year
                . '-'
                . strtoupper(Str::random(8));

        } while (
            CivicIncident::where(
                'incident_number',
                $number
            )->exists()
        );

        return $number;
    }

    /**
     * Haversine distance in meters.
     */
    private function distanceInMeters(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadius = 6371000;

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);

        $deltaLat = deg2rad(
            $lat2 - $lat1
        );

        $deltaLon = deg2rad(
            $lon2 - $lon1
        );

        $a =
            sin($deltaLat / 2) ** 2
            +
            cos($lat1Rad)
            * cos($lat2Rad)
            * sin($deltaLon / 2) ** 2;

        $c = 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );

        return $earthRadius * $c;
    }
}