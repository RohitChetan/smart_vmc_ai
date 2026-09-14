<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [

            /*
            |--------------------------------------------------------------------------
            | GENERAL
            |--------------------------------------------------------------------------
            */

            [
                'key' => 'app.name',
                'group' => 'general',
                'value' => 'Smart Vadodara Connect',
                'type' => 'string',
                'label' => 'Application Name',
                'description' => 'Main application name displayed across the platform.',
            ],

            [
                'key' => 'app.short_name',
                'group' => 'general',
                'value' => 'Smart Vadodara',
                'type' => 'string',
                'label' => 'Short Application Name',
                'description' => 'Short name used in compact UI areas.',
            ],

            [
                'key' => 'app.corporation_name',
                'group' => 'general',
                'value' => 'Vadodara Municipal Corporation',
                'type' => 'string',
                'label' => 'Corporation Name',
                'description' => 'Municipal corporation name.',
            ],

            [
                'key' => 'app.logo',
                'group' => 'general',
                'value' => null,
                'type' => 'string',
                'label' => 'Application Logo',
                'description' => 'Main application logo.',
            ],

            [
                'key' => 'app.favicon',
                'group' => 'general',
                'value' => null,
                'type' => 'string',
                'label' => 'Favicon',
                'description' => 'Browser favicon.',
            ],

            [
                'key' => 'app.contact_email',
                'group' => 'general',
                'value' => null,
                'type' => 'string',
                'label' => 'Contact Email',
                'description' => 'Official contact email.',
            ],

            [
                'key' => 'app.contact_phone',
                'group' => 'general',
                'value' => null,
                'type' => 'string',
                'label' => 'Contact Phone',
                'description' => 'Official contact phone number.',
            ],

            [
                'key' => 'app.website',
                'group' => 'general',
                'value' => null,
                'type' => 'string',
                'label' => 'Website',
                'description' => 'Official website.',
            ],

            /*
            |--------------------------------------------------------------------------
            | EMAIL
            |--------------------------------------------------------------------------
            */

            [
                'key' => 'email.enabled',
                'group' => 'email',
                'value' => '0',
                'type' => 'boolean',
                'label' => 'Enable Email Notifications',
                'description' => 'Enable or disable email notifications.',
            ],

            [
                'key' => 'email.smtp_host',
                'group' => 'email',
                'value' => null,
                'type' => 'string',
                'label' => 'SMTP Host',
                'description' => 'SMTP server hostname.',
            ],

            [
                'key' => 'email.smtp_port',
                'group' => 'email',
                'value' => '587',
                'type' => 'integer',
                'label' => 'SMTP Port',
                'description' => 'SMTP server port.',
            ],

            [
                'key' => 'email.smtp_username',
                'group' => 'email',
                'value' => null,
                'type' => 'string',
                'label' => 'SMTP Username',
                'description' => 'SMTP authentication username.',
            ],

            [
                'key' => 'email.smtp_password',
                'group' => 'email',
                'value' => null,
                'type' => 'string',
                'label' => 'SMTP Password',
                'description' => 'SMTP authentication password.',
                'is_encrypted' => true,
            ],

            [
                'key' => 'email.encryption',
                'group' => 'email',
                'value' => 'tls',
                'type' => 'string',
                'label' => 'Encryption',
                'description' => 'SMTP encryption method.',
            ],

            [
                'key' => 'email.from_name',
                'group' => 'email',
                'value' => 'Smart Vadodara Connect',
                'type' => 'string',
                'label' => 'From Name',
                'description' => 'Name displayed in outgoing emails.',
            ],

            [
                'key' => 'email.from_address',
                'group' => 'email',
                'value' => null,
                'type' => 'string',
                'label' => 'From Email',
                'description' => 'Email address used for outgoing notifications.',
            ],

            /*
            |--------------------------------------------------------------------------
            | WHATSAPP
            |--------------------------------------------------------------------------
            */

            [
                'key' => 'whatsapp.enabled',
                'group' => 'whatsapp',
                'value' => '0',
                'type' => 'boolean',
                'label' => 'Enable WhatsApp Notifications',
                'description' => 'Enable or disable WhatsApp notifications.',
            ],

            [
                'key' => 'whatsapp.provider',
                'group' => 'whatsapp',
                'value' => null,
                'type' => 'string',
                'label' => 'WhatsApp Provider',
                'description' => 'WhatsApp API provider.',
            ],

            [
                'key' => 'whatsapp.api_url',
                'group' => 'whatsapp',
                'value' => null,
                'type' => 'string',
                'label' => 'WhatsApp API URL',
                'description' => 'WhatsApp provider API endpoint.',
            ],

            [
                'key' => 'whatsapp.api_key',
                'group' => 'whatsapp',
                'value' => null,
                'type' => 'string',
                'label' => 'WhatsApp API Key',
                'description' => 'WhatsApp provider API key.',
                'is_encrypted' => true,
            ],

            [
                'key' => 'whatsapp.sender_number',
                'group' => 'whatsapp',
                'value' => null,
                'type' => 'string',
                'label' => 'Sender Number',
                'description' => 'WhatsApp sender number.',
            ],

            /*
            |--------------------------------------------------------------------------
            | NOTIFICATIONS
            |--------------------------------------------------------------------------
            */

            [
                'key' => 'notifications.complaint_created',
                'group' => 'notifications',
                'value' => '1',
                'type' => 'boolean',
                'label' => 'Complaint Created',
                'description' => 'Notify when a new complaint is created.',
            ],

            [
                'key' => 'notifications.complaint_assigned',
                'group' => 'notifications',
                'value' => '1',
                'type' => 'boolean',
                'label' => 'Complaint Assigned',
                'description' => 'Notify when a complaint is assigned.',
            ],

            [
                'key' => 'notifications.status_changed',
                'group' => 'notifications',
                'value' => '1',
                'type' => 'boolean',
                'label' => 'Status Changed',
                'description' => 'Notify when complaint status changes.',
            ],

            [
                'key' => 'notifications.sla_warning',
                'group' => 'notifications',
                'value' => '1',
                'type' => 'boolean',
                'label' => 'SLA Warning',
                'description' => 'Notify when SLA reaches warning threshold.',
            ],

            [
                'key' => 'notifications.sla_breached',
                'group' => 'notifications',
                'value' => '1',
                'type' => 'boolean',
                'label' => 'SLA Breached',
                'description' => 'Notify when SLA is breached.',
            ],

            [
                'key' => 'notifications.resolved',
                'group' => 'notifications',
                'value' => '1',
                'type' => 'boolean',
                'label' => 'Complaint Resolved',
                'description' => 'Notify when complaint is resolved.',
            ],

            [
                'key' => 'notifications.closed',
                'group' => 'notifications',
                'value' => '1',
                'type' => 'boolean',
                'label' => 'Complaint Closed',
                'description' => 'Notify when complaint is closed.',
            ],

            [
                'key' => 'notifications.reopened',
                'group' => 'notifications',
                'value' => '1',
                'type' => 'boolean',
                'label' => 'Complaint Reopened',
                'description' => 'Notify when complaint is reopened.',
            ],

            /*
            |--------------------------------------------------------------------------
            | AI
            |--------------------------------------------------------------------------
            */

            [
                'key' => 'ai.enabled',
                'group' => 'ai',
                'value' => '1',
                'type' => 'boolean',
                'label' => 'AI Pipeline Enabled',
                'description' => 'Enable AI complaint classification and processing.',
            ],

            [
                'key' => 'ai.confidence_threshold',
                'group' => 'ai',
                'value' => '0.50',
                'type' => 'float',
                'label' => 'AI Confidence Threshold',
                'description' => 'Minimum confidence required for AI classification.',
            ],

            [
                'key' => 'ai.yolo_confidence',
                'group' => 'ai',
                'value' => '0.50',
                'type' => 'float',
                'label' => 'YOLO Confidence',
                'description' => 'Minimum YOLO detection confidence.',
            ],

            [
                'key' => 'ai.duplicate_radius',
                'group' => 'ai',
                'value' => '100',
                'type' => 'integer',
                'label' => 'Duplicate Detection Radius',
                'description' => 'Duplicate incident matching radius in meters.',
            ],

            [
                'key' => 'ai.duplicate_window_hours',
                'group' => 'ai',
                'value' => '72',
                'type' => 'integer',
                'label' => 'Duplicate Time Window',
                'description' => 'Duplicate incident matching time window in hours.',
            ],

            /*
            |--------------------------------------------------------------------------
            | GIS
            |--------------------------------------------------------------------------
            */

            [
                'key' => 'gis.enabled',
                'group' => 'gis',
                'value' => '1',
                'type' => 'boolean',
                'label' => 'GIS Routing Enabled',
                'description' => 'Enable automatic GIS ward routing.',
            ],

            [
                'key' => 'gis.default_latitude',
                'group' => 'gis',
                'value' => '22.3072',
                'type' => 'float',
                'label' => 'Default Latitude',
                'description' => 'Default map center latitude.',
            ],

            [
                'key' => 'gis.default_longitude',
                'group' => 'gis',
                'value' => '73.1812',
                'type' => 'float',
                'label' => 'Default Longitude',
                'description' => 'Default map center longitude.',
            ],

            [
                'key' => 'gis.default_zoom',
                'group' => 'gis',
                'value' => '12',
                'type' => 'integer',
                'label' => 'Default Map Zoom',
                'description' => 'Default Leaflet map zoom.',
            ],

            [
                'key' => 'gis.gps_accuracy_threshold',
                'group' => 'gis',
                'value' => '100',
                'type' => 'integer',
                'label' => 'GPS Accuracy Threshold',
                'description' => 'Maximum accepted GPS accuracy in meters.',
            ],

            /*
            |--------------------------------------------------------------------------
            | REWARDS
            |--------------------------------------------------------------------------
            */

            [
                'key' => 'rewards.enabled',
                'group' => 'rewards',
                'value' => '1',
                'type' => 'boolean',
                'label' => 'Citizen Rewards Enabled',
                'description' => 'Enable citizen civic points and rewards.',
            ],

            [
                'key' => 'rewards.points_per_verification',
                'group' => 'rewards',
                'value' => '10',
                'type' => 'integer',
                'label' => 'Points Per Verification',
                'description' => 'Civic points awarded after successful citizen verification.',
            ],

            [
                'key' => 'rewards.certificate_enabled',
                'group' => 'rewards',
                'value' => '1',
                'type' => 'boolean',
                'label' => 'Certificate Enabled',
                'description' => 'Enable Jagruk Nagrik certificate generation.',
            ],

            /*
            |--------------------------------------------------------------------------
            | SECURITY
            |--------------------------------------------------------------------------
            */

            [
                'key' => 'security.session_timeout',
                'group' => 'security',
                'value' => '120',
                'type' => 'integer',
                'label' => 'Session Timeout',
                'description' => 'Session timeout in minutes.',
            ],

            [
                'key' => 'security.login_attempt_limit',
                'group' => 'security',
                'value' => '5',
                'type' => 'integer',
                'label' => 'Login Attempt Limit',
                'description' => 'Maximum failed login attempts.',
            ],

        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'group' => $setting['group'],
                    'value' => $setting['value'] ?? null,
                    'type' => $setting['type'] ?? 'string',
                    'label' => $setting['label'] ?? null,
                    'description' => $setting['description'] ?? null,
                    'is_active' => $setting['is_active'] ?? true,
                    'is_encrypted' => $setting['is_encrypted'] ?? false,
                ]
            );
        }
    }
}
