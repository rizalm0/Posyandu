<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isKader() && $user->posyandu_id !== null;
    }

    public function create(User $user): bool
    {
        return $user->isKader() && $user->posyandu_id !== null;
    }

    public function update(User $user, User $account): bool
    {
        if (! $user->isKader() || ! $account->isParent() || $user->posyandu_id === null) {
            return false;
        }

        return $account->posyandu_id === null || $account->posyandu_id === $user->posyandu_id;
    }
}
