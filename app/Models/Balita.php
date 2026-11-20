<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'posyandu_id',
    'registration_number',
    'full_name',
    'date_of_birth',
    'gender',
    'parent_name',
    'phone',
    'address',
    'registered_at',
    'is_active',
])]
class Balita extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'registered_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function posyandu(): BelongsTo
    {
        return $this->belongsTo(Posyandu::class);
    }

    public function examinations(): HasMany
    {
        return $this->hasMany(Examination::class);
    }

    public function latestExamination(): HasOne
    {
        return $this->hasOne(Examination::class)->latestOfMany('examined_at');
    }

    public function immunizations(): HasMany
    {
        return $this->hasMany(Immunization::class)->latest('administered_at');
    }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['relationship', 'is_primary', 'verified_at'])
            ->withTimestamps();
    }

    public function verifiedGuardians(): BelongsToMany
    {
        return $this->guardians()->wherePivotNotNull('verified_at');
    }

    protected function initials(): Attribute
    {
        return Attribute::get(function (): string {
            $words = preg_split('/\s+/', trim((string) $this->full_name)) ?: [];
            $initials = '';

            foreach (array_slice($words, 0, 2) as $word) {
                $initials .= mb_strtoupper(mb_substr($word, 0, 1));
            }

            return $initials !== '' ? $initials : '?';
        });
    }

    protected function genderLabel(): Attribute
    {
        return Attribute::get(fn (): string => $this->gender === 'L' ? 'Laki-laki' : 'Perempuan');
    }

    protected function ageInMonths(): Attribute
    {
        return Attribute::get(fn (): int => max(0, (int) $this->date_of_birth->diffInMonths(today())));
    }

    protected function ageLabel(): Attribute
    {
        return Attribute::get(fn (): string => umur_balita($this->date_of_birth, today()));
    }

    protected function hasVerifiedGuardian(): Attribute
    {
        return Attribute::get(function (): bool {
            if ($this->relationLoaded('verifiedGuardians')) {
                return $this->verifiedGuardians->isNotEmpty();
            }

            return $this->verifiedGuardians()->exists();
        });
    }

    protected function lastExaminedAt(): Attribute
    {
        return Attribute::get(fn () => $this->latestExamination?->examined_at);
    }

    protected function isOverdue(): Attribute
    {
        return Attribute::get(function (): bool {
            $days = $this->posyandu?->alert_no_exam_days ?? 60;
            $last = $this->latestExamination?->examined_at;

            return $last === null || $last->lt(today()->subDays($days));
        });
    }

    protected function dataStatus(): Attribute
    {
        return Attribute::get(function (): array {
            if ($this->isOverdue) {
                return ['label' => 'Belum diperiksa', 'tone' => 'warning'];
            }

            if ($this->latestExamination === null || $this->latestExamination->height_cm === null) {
                return ['label' => 'Data perlu dilengkapi', 'tone' => 'warning'];
            }

            if (! $this->has_verified_guardian) {
                return ['label' => 'Akun orang tua belum terhubung', 'tone' => 'neutral'];
            }

            return ['label' => 'Data lengkap', 'tone' => 'success'];
        });
    }

    public function examinationsChronological(): HasMany
    {
        return $this->examinations()->oldest('examined_at');
    }
}
