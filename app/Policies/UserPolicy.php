<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if a given user has permission to show
     *
     * @param User $currentUser
     * @param User $user
     * @return bool
     */
    public function show(User $currentUser, User $user)
    {
        if ($currentUser->isAdmin()) {
            return true;
        }

        return $currentUser->id === $user->id;
    }

    /**
     * Determine if a given user has permission to create
     *
     * @param User $currentUser
     * @return bool
     */
    public function create(User $currentUser)
    {
        if ($currentUser->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if a given user can update
     *
     * @param User $currentUser
     * @param User $user
     * @return bool
     */
    public function update(User $currentUser, User $user)
    {
        if ($currentUser->isAdmin()) {
            return true;
        }

        return $currentUser->id === $user->id;
    }

    /**
     * Determine if a given user can delete
     *
     * @param User $currentUser
     * @param User $user
     * @return bool
     */
    public function destroy(User $currentUser, User $user)
    {
        if ($currentUser->id === $user->id) {
            return  false;
        }

        return  $currentUser->isAdmin();
    }
}
