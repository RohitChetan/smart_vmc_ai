<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\SystemSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class AdminSettingsController extends Controller
{
    public function __construct(
        protected SystemSettingService $settings
    ) {
    }

    /**
     * Return all active settings grouped by category.
     */
    public function index(): JsonResponse
    {
        $settings = SystemSetting::query()
            ->where('is_active', true)
            ->orderBy('group')
            ->orderBy('id')
            ->get();

        $groups = $settings
            ->groupBy('group')
            ->map(function ($items) {
                return $items->map(function ($setting) {
                    return $this->formatSetting($setting);
                })->values()->toArray();
            });

        return response()->json([
            'success' => true,
            'settings' => $groups,
        ]);
    }

    /**
     * Return one settings group.
     */
    public function group(string $group): JsonResponse
    {
        $allowedGroups = [
            'general',
            'email',
            'whatsapp',
            'notifications',
            'ai',
            'gis',
            'rewards',
            'security',
        ];

        if (!in_array($group, $allowedGroups, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid settings group.',
            ], 422);
        }

        $settings = SystemSetting::query()
            ->where('group', $group)
            ->where('is_active', true)
            ->orderBy('id')
            ->get()
            ->map(function ($setting) {
                return $this->formatSetting($setting);
            })
            ->values();

        return response()->json([
            'success' => true,
            'group' => $group,
            'settings' => $settings,
        ]);
    }

    /**
     * Update multiple settings.
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'settings' => ['required', 'array', 'min:1'],

            'settings.*.key' => [
                'required',
                'string',
                'max:150',
                Rule::exists('system_settings', 'key'),
            ],

            'settings.*.value' => [
                'nullable',
            ],
        ]);

        $updated = [];

        foreach ($validated['settings'] as $item) {
            $setting = SystemSetting::query()
                ->where('key', $item['key'])
                ->first();

            if (!$setting) {
                continue;
            }

            /*
             * For encrypted fields, an empty value means:
             * keep the existing secret instead of deleting it.
             */
            if (
                $setting->is_encrypted &&
                ($item['value'] === null || $item['value'] === '')
            ) {
                $updated[] = $setting->key;
                continue;
            }

            $this->validateSettingValue($setting, $item['value']);

            $setting->setSettingValue($item['value']);

            $updated[] = $setting->key;
        }

        $this->settings->clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully.',
            'updated' => $updated,
        ]);
    }

    /**
     * Update one setting.
     */
    public function updateOne(
        Request $request,
        string $key
    ): JsonResponse {
        $setting = SystemSetting::query()
            ->where('key', $key)
            ->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found.',
            ], 404);
        }

        $request->validate([
            'value' => ['nullable'],
        ]);

        $value = $request->input('value');

        if (
            $setting->is_encrypted &&
            ($value === null || $value === '')
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Encrypted setting value cannot be empty.',
            ], 422);
        }

        $this->validateSettingValue($setting, $value);

        $setting->setSettingValue($value);

        $this->settings->clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully.',
            'setting' => $this->formatSetting($setting->fresh()),
        ]);
    }

    /**
     * Upload application logo.
     */
    public function uploadLogo(Request $request): JsonResponse
    {
        $request->validate([
            'logo' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,svg',
                'max:4096',
            ],
        ]);

        $path = $request->file('logo')->store(
            'settings',
            'public'
        );

        $setting = SystemSetting::firstOrCreate(
            ['key' => 'app.logo'],
            [
                'group' => 'general',
                'type' => 'string',
                'label' => 'Application Logo',
                'is_active' => true,
                'is_encrypted' => false,
            ]
        );

        /*
         * Remove previous logo if it belongs to our public disk.
         */
        if (
            $setting->value &&
            !str_starts_with($setting->value, 'http')
        ) {
            Storage::disk('public')->delete(
                $setting->value
            );
        }

        $setting->value = $path;
        $setting->save();

        $this->settings->clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Application logo uploaded successfully.',
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    /**
     * Upload favicon.
     */
    public function uploadFavicon(Request $request): JsonResponse
    {
        $request->validate([
            'favicon' => [
                'required',
                'file',
                'mimes:ico,png,jpg,jpeg,webp,svg',
                'max:2048',
            ],
        ]);

        $path = $request->file('favicon')->store(
            'settings',
            'public'
        );

        $setting = SystemSetting::firstOrCreate(
            ['key' => 'app.favicon'],
            [
                'group' => 'general',
                'type' => 'string',
                'label' => 'Favicon',
                'is_active' => true,
                'is_encrypted' => false,
            ]
        );

        if (
            $setting->value &&
            !str_starts_with($setting->value, 'http')
        ) {
            Storage::disk('public')->delete(
                $setting->value
            );
        }

        $setting->value = $path;
        $setting->save();

        $this->settings->clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Favicon uploaded successfully.',
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    /**
     * Return safe setting representation.
     *
     * Encrypted values are NEVER returned to frontend.
     */
    protected function formatSetting(
        SystemSetting $setting
    ): array {
        $value = null;

        if (!$setting->is_encrypted) {
            $value = $setting->typed_value;
        }

        return [
            'id' => $setting->id,
            'key' => $setting->key,
            'group' => $setting->group,
            'value' => $value,
            'type' => $setting->type,
            'label' => $setting->label,
            'description' => $setting->description,
            'is_active' => $setting->is_active,
            'is_encrypted' => $setting->is_encrypted,
            'has_value' => $setting->value !== null
                && $setting->value !== '',
        ];
    }

    /**
     * Validate a setting according to its stored type/key.
     */
    protected function validateSettingValue(
        SystemSetting $setting,
        mixed $value
    ): void {
        if ($value === null) {
            return;
        }

        switch ($setting->type) {
            case 'boolean':
                if (
                    !is_bool($value) &&
                    !in_array(
                        $value,
                        [0, 1, '0', '1', 'true', 'false'],
                        true
                    )
                ) {
                    abort(
                        response()->json([
                            'success' => false,
                            'message' => "{$setting->key} must be boolean.",
                        ], 422)
                    );
                }
                break;

            case 'integer':
                if (
                    filter_var(
                        $value,
                        FILTER_VALIDATE_INT
                    ) === false
                ) {
                    abort(
                        response()->json([
                            'success' => false,
                            'message' => "{$setting->key} must be an integer.",
                        ], 422)
                    );
                }
                break;

            case 'float':
                if (!is_numeric($value)) {
                    abort(
                        response()->json([
                            'success' => false,
                            'message' => "{$setting->key} must be numeric.",
                        ], 422)
                    );
                }
                break;

            case 'json':
                if (!is_array($value)) {
                    abort(
                        response()->json([
                            'success' => false,
                            'message' => "{$setting->key} must be an array.",
                        ], 422)
                    );
                }
                break;
        }
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $settings = app(\App\Services\SystemSettingService::class);

        if (!$settings->get('email.enabled', false)) {
            return response()->json([
                'message' => 'Email notifications are disabled in Settings.',
            ], 422);
        }

        $host = $settings->get('email.smtp_host');
        $port = $settings->get('email.smtp_port', 587);
        $username = $settings->get('email.smtp_username');
        $password = $settings->get('email.smtp_password');
        $encryption = $settings->get('email.encryption');

        $fromAddress = $settings->get(
            'email.from_address',
            $settings->get('app.contact_email', 'hello@example.com')
        );

        $fromName = $settings->get(
            'email.from_name',
            $settings->get('app.name', 'Smart Vadodara Connect')
        );

        if (!$host) {
            return response()->json([
                'message' => 'SMTP Host is not configured.',
            ], 422);
        }

        if (!$fromAddress) {
            return response()->json([
                'message' => 'From Email is not configured.',
            ], 422);
        }

        /*
        * Build SMTP configuration from Admin Settings.
        * This does NOT require changing .env.
        */
        Config::set('mail.default', 'smtp');

        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', $host);
        Config::set('mail.mailers.smtp.port', (int) $port);
        Config::set('mail.mailers.smtp.username', $username ?: null);
        Config::set('mail.mailers.smtp.password', $password ?: null);
        // Config::set('mail.mailers.smtp.scheme', $encryption ?: null);

        $encryption = strtolower(trim((string) $encryption));

        if ($encryption === 'tls') {
            // Port 587 uses STARTTLS; do not set scheme=tls.
            Config::set('mail.mailers.smtp.scheme', null);
        } elseif ($encryption === 'ssl') {
            // SMTPS is normally used with port 465.
            Config::set('mail.mailers.smtp.scheme', 'smtps');
        } else {
            Config::set('mail.mailers.smtp.scheme', null);
        }

        Config::set('mail.from.address', $fromAddress);
        Config::set('mail.from.name', $fromName);

        /*
         * The SMTP mailer may already be resolved in the current Laravel
         * request. Purge it so the test uses the SMTP settings configured
         * above instead of a previously cached mailer instance.
         */
        Mail::purge('smtp');

        try {

            Mail::raw(
                "This is a test email from {$fromName}.\n\n"
                . "Your Smart Vadodara Connect SMTP configuration is working correctly.\n\n"
                . "If you received this email, the notification email system is ready.",
                function ($message) use ($request, $fromAddress, $fromName) {

                    $message
                        ->to($request->email)
                        ->from($fromAddress, $fromName)
                        ->subject('Smart Vadodara Connect - Test Email');
                }
            );

            return response()->json([
                'message' => 'Test email sent successfully.',
            ]);

        } catch (\Throwable $e) {

            report($e);

            return response()->json([
                'message' => 'SMTP connection failed.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : 'Unable to connect to the configured SMTP server. Check SMTP host, port, encryption and credentials.',
            ], 422);
        }
    }
}