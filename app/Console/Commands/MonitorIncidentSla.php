<?php

namespace App\Console\Commands;

use App\Services\SlaMonitorService;
use Illuminate\Console\Command;

class MonitorIncidentSla extends Command
{
    protected $signature = 'sla:monitor';

    protected $description = 'Monitor civic incident SLA and create escalations';

    public function handle(SlaMonitorService $slaMonitorService): int
    {
        $this->info('Starting Smart Vadodara SLA monitor...');

        $summary = $slaMonitorService->monitor();

        $this->newLine();

        $this->table(
            [
                'Metric',
                'Count',
            ],
            [
                ['Incidents checked', $summary['checked']],
                ['Warnings created', $summary['warning']],
                ['SLA breaches created', $summary['breached']],
                ['Critical escalations created', $summary['critical']],
                ['Escalations resolved', $summary['resolved']],
            ]
        );

        $this->newLine();

        $this->info('SLA monitor completed.');

        return self::SUCCESS;
    }
}