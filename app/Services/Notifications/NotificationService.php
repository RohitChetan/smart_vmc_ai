<?php

namespace App\Services\Notifications;

use App\Models\Complaint;
use App\Models\User;
use App\Notifications\CivicSystemNotification;
use App\Services\SystemSettingService;

class NotificationService
{
    public function __construct(
        protected SystemSettingService $settings
    ) {
    }

    /**
     * Send a civic notification according to Admin Settings.
     */
    public function send(
        string $event,
        User $user,
        Complaint $complaint,
        array $data = []
    ): void {
        if (!$user) {
            return;
        }

        $channels = $this->resolveChannels($event);

        if (empty($channels)) {
            return;
        }

        $title = $data['title']
            ?? $this->defaultTitle($event);

        $message = $data['message']
            ?? $this->defaultMessage($event, $complaint);

        $url = $data['url'] ?? null;

        $notificationData = [
            'complaint_id' => $complaint->id,
            'complaint_number' => $complaint->complaint_number,
            'event' => $event,
            ...($data['data'] ?? []),
        ];

        $user->notify(
            new CivicSystemNotification(
                title: $title,
                message: $message,
                type: $event,
                url: $url,
                data: $notificationData,
                channels: $channels,
            )
        );
    }

    /**
     * Resolve channels from Admin Settings.
     *
     * Example:
     * notifications.complaint_assigned.email
     * notifications.complaint_assigned.whatsapp
     * notifications.complaint_assigned.in_app
     */
    protected function resolveChannels(string $event): array
    {
        $channels = [];

        /*
         * Master event switch.
         */
        $eventEnabled = $this->settings->get(
            "notifications.{$event}",
            true
        );

        if (!$this->toBoolean($eventEnabled)) {
            return [];
        }

        /*
         * In-App
         */
        if (
            $this->toBoolean(
                $this->settings->get(
                    "notifications.{$event}.in_app",
                    false
                )
            )
        ) {
            $channels[] = 'database';
        }

        /*
         * Email
         */
        if (
            $this->toBoolean(
                $this->settings->get(
                    "notifications.{$event}.email",
                    false
                )
            )
            &&
            $this->toBoolean(
                $this->settings->get(
                    'email.enabled',
                    false
                )
            )
        ) {
            if (!empty($channels)) {
                $channels[] = 'mail';
            } else {
                $channels[] = 'mail';
            }
        }

        /*
         * WhatsApp
         *
         * Actual provider integration will be connected
         * after the WhatsApp API provider is configured.
         */
        if (
            $this->toBoolean(
                $this->settings->get(
                    "notifications.{$event}.whatsapp",
                    false
                )
            )
            &&
            $this->toBoolean(
                $this->settings->get(
                    'whatsapp.enabled',
                    false
                )
            )
        ) {
            /*
             * Do not add Laravel channel yet.
             * WhatsApp provider implementation comes next.
             */
        }

        return array_values(
            array_unique($channels)
        );
    }

    protected function toBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return filter_var(
            $value,
            FILTER_VALIDATE_BOOLEAN
        );
    }

    protected function defaultTitle(string $event): string
    {
        return match ($event) {
            'complaint_created'
                => 'Complaint Registered',

            'complaint_assigned'
                => 'Complaint Assigned',

            'status_changed'
                => 'Complaint Status Updated',

            'sla_warning'
                => 'SLA Warning',

            'sla_breached'
                => 'SLA Breached',

            'resolved'
                => 'Complaint Resolved',

            'closed'
                => 'Complaint Closed',

            'reopened'
                => 'Complaint Reopened',

            default
                => 'Smart Vadodara Connect Update',
        };
    }

    protected function defaultMessage(
        string $event,
        Complaint $complaint
    ): string {
        $number = $complaint->complaint_number;

        return match ($event) {
            'complaint_created'
                => "Your complaint {$number} has been successfully registered.",

            'complaint_assigned'
                => "Your complaint {$number} has been assigned to the concerned team.",

            'status_changed'
                => "The status of your complaint {$number} has been updated.",

            'sla_warning'
                => "Your complaint {$number} is approaching its SLA deadline.",

            'sla_breached'
                => "The SLA for your complaint {$number} has been breached.",

            'resolved'
                => "Your complaint {$number} has been marked as resolved.",

            'closed'
                => "Your complaint {$number} has been closed.",

            'reopened'
                => "Your complaint {$number} has been reopened for further action.",

            default
                => "There is an update regarding your complaint {$number}.",
        };
    }
}