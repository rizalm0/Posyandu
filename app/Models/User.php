<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['posyandu_id', 'name', 'email', 'phone', 'role', 'password', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function posyandu(): BelongsTo
    {
        return $this->belongsTo(Posyandu::class);
    }

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Balita::class)
            ->withPivot(['relationship', 'is_primary', 'verified_at'])
            ->withTimestamps();
    }

    public function verifiedChildren(): BelongsToMany
    {
        return $this->children()->wherePivotNotNull('verified_at');
    }

    public function recordedExaminations(): HasMany
    {
        return $this->hasMany(Examination::class, 'recorded_by');
    }

    public function isKader(): bool
    {
        return $this->role === 'kader';
    }

    public function isParent(): bool
    {
        return $this->role === 'parent';
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    protected function initials(): Attribute
    {
        return Attribute::get(function (): string {
            $words = preg_split('/\s+/', trim((string) $this->name)) ?: [];
            $initials = '';

            foreach (array_slice($words, 0, 2) as $word) {
                $initials .= mb_strtoupper(mb_substr($word, 0, 1));
            }

            return $initials !== '' ? $initials : '?';
        });
    }

    public function homeRoute(): string
    {
        return $this->isKader() ? route('kader.dashboard') : route('orang-tua.dashboard');
    }
}
