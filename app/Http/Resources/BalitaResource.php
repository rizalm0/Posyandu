<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BalitaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'posyandu_id' => $this->posyandu_id,
            'registration_number' => $this->registration_number,
            'full_name' => $this->full_name,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'gender' => $this->gender,
            'gender_label' => $this->gender_label,
            'parent_name' => $this->parent_name,
            'phone' => $this->phone,
            'address' => $this->address,
            'registered_at' => $this->registered_at?->format('Y-m-d'),
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
