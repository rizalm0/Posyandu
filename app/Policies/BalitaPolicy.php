<?php

namespace App\Policies;

use App\Models\Balita;
use App\Models\User;

class BalitaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isKader();
    }

    public function view(User $user, Balita $balita): bool
    {
        if ($user->isKader()) {
            return $user->posyandu_id !== null && $user->posyandu_id === $balita->posyandu_id;
        }

        return $user->isParent()
            && $user->verifiedChildren()->whereKey($balita->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->isKader() && $user->posyandu_id !== null;
    }

    public function update(User $user, Balita $balita): bool
    {
        return $user->isKader()
            && $user->posyandu_id !== null
            && $user->posyandu_id === $balita->posyandu_id;
    }

    public function delete(User $user, Balita $balita): bool
    {
        return false;
    }
}
