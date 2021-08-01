<?php


namespace App\Repositories;


use App\Models\User;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Database\Eloquent\Model;

class EloquentUserRepository extends AbstractEloquentRepository implements UserRepository
{
    /**
     * `
     * @inheritdoc
     * @throws \Throwable
     */
    public function save(array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        return $this->getModel()->getConnection()->transaction(function () use ($data) {
            /** @var User $user */
            $user = parent::save($data);
            return $user->syncRoles($data['roles'] ?? ['member']);
        });
    }

    /**
     * @inheritdoc
     * @throws \Throwable
     */
    public function update(Model $model, array $data)
    {
        return $this->getModel()->getConnection()->transaction(function () use ($model, $data) {
            unset($data['password']);
            /** @var User $user */
            $user = parent::update($model, $data);
            if (empty($data['roles'])) {
                return $user;
            }
            return $user->syncRoles($data['roles']);
        });
    }

    public function findOne($id, array $relations = null)
    {
        if ($id === 'me') {
            $id = $this->getLoggedInUser()->id;
        }

        return parent::findOne($id);
    }
}
