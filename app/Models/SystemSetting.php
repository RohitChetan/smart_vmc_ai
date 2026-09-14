<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'group',
        'value',
        'type',
        'label',
        'description',
        'is_active',
        'is_encrypted',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_encrypted' => 'boolean',
    ];

    /**
     * Get the setting value with type conversion.
     */
    public function getTypedValueAttribute()
    {
        if ($this->value === null) {
            return null;
        }

        if ($this->is_encrypted) {
            try {
                return decrypt($this->value);
            } catch (\Throwable $e) {
                return null;
            }
        }

        return match ($this->type) {
            'boolean' => filter_var(
                $this->value,
                FILTER_VALIDATE_BOOLEAN
            ),

            'integer' => (int) $this->value,

            'float' => (float) $this->value,

            'json' => json_decode(
                $this->value,
                true
            ),

            default => $this->value,
        };
    }

    /**
     * Store a value securely when required.
     */
    public function setSettingValue($value): void
    {
        if ($this->is_encrypted && $value !== null && $value !== '') {
            $this->value = encrypt($value);
        } else {
            $this->value = $this->castValueForStorage($value);
        }

        $this->save();
    }

    /**
     * Convert values before database storage.
     */
    protected function castValueForStorage($value): ?string
    {
        if ($value === null) {
            return null;
        }

        return match ($this->type) {
            'boolean' => $value ? '1' : '0',

            'integer' => (string) ((int) $value),

            'float' => (string) ((float) $value),

            'json' => json_encode(
                $value,
                JSON_UNESCAPED_UNICODE
            ),

            default => (string) $value,
        };
    }
}
