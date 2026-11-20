<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'balita_id',
    'name',
    'administered_at',
    'status',
    'notes',
    'recorded_by',
])]
class Immunization extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'administered_at' => 'date',
        ];
    }

    public function balita(): BelongsTo
    {
        return $this->belongsTo(Balita::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn (): string => $this->status === 'needs_review' ? 'Perlu ditinjau' : 'Tercatat');
    }
}
