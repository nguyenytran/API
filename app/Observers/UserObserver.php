<?php


namespace App\Observers;


use App\Models\User;

class UserObserver
{
    public function creating(User $user)
    {
        $currentUser = user();

        if ($currentUser) {
            $user->created_by = $currentUser->id;
        }
    }

    public function saved(User $user)
    {
        if ($user->getRoleNames()->isEmpty()) {
            $user->syncRoles("member");
        }
    }
}
