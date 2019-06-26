<?php


namespace App\Transformers\Common;


use App\Models\User;
use App\Transformers\AbstractTransformer;

class UserTransformer extends AbstractTransformer
{
    protected $availableIncludes = [
        'permissions'
    ];

    protected $defaultIncludes = [
        'roles',
    ];

    public function transform(User $user)
    {
        return [
            'id'                => $user->id,
            'name'              => $user->name,
            'username'          => $user->username,
            'email'             => $user->email,
            'gender'            => $user->gender,
            'address'           => $user->address,
            'phone'             => $user->phone,
            'profile_picture'   => $user->profile_picture,
            'is_active'         => $user->is_active,
            'created_at'        => (string) $user->created_at,
            'updated_at'        => (string) $user->updated_at,
        ];
    }

    /**
     * @param \App\Models\User $user
     * @return \League\Fractal\Resource\NullResource|\League\Fractal\Resource\Primitive
     * @throws \Exception
     */
    public function includeRoles(User $user)
    {
        return $this->primitive($user->getRoleNames(), null, 'permissions');
    }

    /**
     * @param \App\Models\User $user
     * @return \League\Fractal\Resource\NullResource|\League\Fractal\Resource\Primitive
     * @throws \Exception
     */
    public function includePermissions(User $user)
    {
        return $this->primitive($user->getAllPermissions()->pluck("name"), null, 'permissions');
    }
}
