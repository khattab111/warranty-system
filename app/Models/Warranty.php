<?php

namespace App\Models;

use Database\Factories\WarrantyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Warranty extends Model
{
    /** @use HasFactory<WarrantyFactory> */
    use HasFactory;

    protected $fillable = [
        'public_token',
        'device_type',
        'imei',
        'warranty_expires_at',
        'activated_at',
        'printed_at',
    ];

    protected function casts(): array
    {
        return [
            'warranty_expires_at' => 'datetime',
            'activated_at' => 'datetime',
            'printed_at' => 'datetime',
        ];
    }

    public function getIsPrintedAttribute(): bool
    {
        return $this->printed_at !== null;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->activated_at !== null && ! $this->is_expired;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->warranty_expires_at !== null && $this->warranty_expires_at->isPast();
    }

    public function getStatusAttribute(): string
    {
        if ($this->activated_at === null) {
            return 'inactive';
        }

        return $this->is_expired ? 'expired' : 'active';
    }

    public function getWorkflowStatusAttribute(): string
    {
        if ($this->activated_at === null) {
            return $this->printed_at === null ? 'unprinted_unlinked' : 'printed_pending';
        }

        return 'linked_completed';
    }

    public function getRemainingDaysAttribute(): ?array
    {
        if ($this->warranty_expires_at === null || $this->is_expired) {
            return null;
        }

        $diff = $this->warranty_expires_at->diff(Carbon::now());

        return [
            'days' => $diff->days,
            'hours' => $diff->h,
            'minutes' => $diff->i,
        ];
    }

    public function getMaskedImeiAttribute(): string
    {
        if ($this->imei === null) {
            return '';
        }

        return str_repeat('*', 11).substr($this->imei, -4);
    }

    public function getShortReferenceAttribute(): string
    {
        return strtoupper(substr($this->public_token, 0, 8));
    }

    public static function validateActivationData(array $data, ?int $ignoreId = null): array
    {
        $sanitized = [
            'device_type' => trim(strip_tags((string) ($data['device_type'] ?? ''))),
            'imei' => trim((string) ($data['imei'] ?? '')),
            'warranty_expires_at' => $data['warranty_expires_at'] ?? null,
        ];

        $validator = Validator::make($sanitized, [
            'device_type' => ['required', 'string', 'max:150'],
            'imei' => ['required', 'string', 'digits:15'],
            'warranty_expires_at' => ['required', 'date', 'after:today'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $query = static::query()->where('imei', $sanitized['imei']);

        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'imei' => ['هذا الرقم المسلسل موجود بالفعل.'],
            ]);
        }

        return $sanitized;
    }

    public function isPendingActivation(): bool
    {
        return $this->activated_at === null;
    }

    protected static function booted(): void
    {
        static::creating(function (Warranty $warranty) {
            if (empty($warranty->public_token)) {
                $warranty->public_token = (string) Str::uuid();
            }
        });
    }
}
