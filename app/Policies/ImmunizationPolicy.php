<?php

namespace App\Policies;

use App\Models\Balita;
use App\Models\Immunization;
use App\Models\User;

class ImmunizationPolicy
{
    public function view(User $user, Immunization $immunization): bool
    {
        if ($user->isKader()) {
            return $user->posyandu_id !== null
                && $user->posyandu_id === $immunization->balita->posyandu_id;
        }

        return $user->isParent()
            && $user->verifiedChildren()->whereKey($immunization->balita_id)->exists();
    }

    public function create(User $user, Balita $balita): bool
    {
        return $user->isKader()
            && $user->posyandu_id !== null
            && $user->posyandu_id === $balita->posyandu_id;
    }

    public function update(User $user, Immunization $immunization): bool
    {
        return $user->isKader()
            && $user->posyandu_id !== null
            && $user->posyandu_id === $immunization->balita->posyandu_id;
    }
}
