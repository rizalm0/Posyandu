<?php

namespace App\Policies;

use App\Models\Balita;
use App\Models\Examination;
use App\Models\User;

class ExaminationPolicy
{
    public function view(User $user, Examination $examination): bool
    {
        if ($user->isKader()) {
            return $user->posyandu_id !== null && $user->posyandu_id === $examination->posyandu_id;
        }

        return $user->isParent()
            && $user->verifiedChildren()->whereKey($examination->balita_id)->exists();
    }

    public function create(User $user, Balita $balita): bool
    {
        return $user->isKader()
            && $user->posyandu_id !== null
            && $user->posyandu_id === $balita->posyandu_id;
    }

    public function update(User $user, Examination $examination): bool
    {
        return $user->isKader()
            && $user->posyandu_id !== null
            && $user->posyandu_id === $examination->posyandu_id;
    }

    public function delete(User $user, Examination $examination): bool
    {
        return false;
    }
}
