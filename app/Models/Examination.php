<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'posyandu_id',
    'balita_id',
    'examined_at',
    'weight_kg',
    'height_cm',
    'head_circumference_cm',
    'notes',
    'recorded_by',
])]
class Examination extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'examined_at' => 'date',
            'weight_kg' => 'decimal:2',
            'height_cm' => 'decimal:1',
            'head_circumference_cm' => 'decimal:1',
        ];
    }

    public function balita(): BelongsTo
    {
        return $this->belongsTo(Balita::class);
    }

    public function posyandu(): BelongsTo
    {
        return $this->belongsTo(Posyandu::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    protected function isComplete(): Attribute
    {
        return Attribute::get(fn (): bool => $this->weight_kg !== null && $this->height_cm !== null);
    }

    protected function ageAtExamination(): Attribute
    {
        return Attribute::get(fn (): string => umur_balita($this->balita->date_of_birth, $this->examined_at));
    }
}
