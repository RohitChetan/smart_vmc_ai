<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class SystemSettingService
{
    /**
     * Get a setting.
     */
    public function get(
        string $key,
        mixed $default = null
    ): mixed {
        $setting = SystemSetting::query()
            ->where('key', $key)
            ->where('is_active', true)
            ->first();

        if (!$setting) {
            return $default;
        }

        return $setting->typed_value ?? $default;
    }

    /**
     * Get all settings for a group.
     */
    public function group(string $group): array
    {
        return SystemSetting::query()
            ->where('group', $group)
            ->where('is_active', true)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [
                    $setting->key => $setting->typed_value,
                ];
            })
            ->toArray();
    }

    /**
     * Create or update a setting.
     */
    public function set(
        string $key,
        mixed $value,
        array $options = []
    ): SystemSetting {
        $setting = SystemSetting::firstOrNew([
            'key' => $key,
        ]);

        $setting->group = $options['group']
            ?? $setting->group
            ?? 'general';

        $setting->type = $options['type']
            ?? $setting->type
            ?? $this->detectType($value);

        $setting->label = $options['label']
            ?? $setting->label;

        $setting->description = $options['description']
            ?? $setting->description;

        $setting->is_active = $options['is_active']
            ?? $setting->is_active
            ?? true;

        $setting->is_encrypted = $options['is_encrypted']
            ?? $setting->is_encrypted
            ?? false;

        $setting->setSettingValue($value);

        return $setting->fresh();
    }

    /**
     * Update multiple settings at once.
     */
    public function setMany(
        array $settings
    ): void {
        foreach ($settings as $key => $config) {
            $value = $config['value'] ?? null;

            $this->set(
                $key,
                $value,
                $config
            );
        }
    }

    /**
     * Detect basic setting type.
     */
    protected function detectType(mixed $value): string
    {
        return match (true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_float($value) => 'float',
            is_array($value) => 'json',
            default => 'string',
        };
    }

    /**
     * Clear cached settings.
     */
    public function clearCache(): void
    {
        Cache::forget('system_settings');
    }
}
