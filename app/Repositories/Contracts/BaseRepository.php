<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepository
{
    /**
     * Find a resource by id
     *
     * @param $id
     * @param array $relations
     * @return Model|null
     */
    public function findOne($id, array $relations = null);

    /**
     * Find a resource by criteria
     *
     * @param array $criteria
     * @param \Closure|null $builder
     * @return Model|null
     */
    public function findOneBy(array $criteria, \Closure $builder = null);

    /**
     * Search All resources by criteria
     *
     * @param array $searchCriteria
     * @param \Closure|null $builder
     * @param Bool $paginate
     * @return Collection
     */
    public function findBy(array $searchCriteria = [], \Closure $builder = null, $paginate = true);

    /**
     * Search All resources by any values of a key
     *
     * @param string $key
     * @param array $values
     * @return Collection
     */
    public function findIn($key, array $values);

    /**
     * Save a resource
     *
     * @param array $data
     * @return Model
     */
    public function save(array $data);

    /**
     * Update a resource
     *
     * @param Model $model
     * @param array $data
     * @return Model
     */
    public function update(Model $model, array $data);

    /**
     * Delete a resource
     *
     * @param Model $model
     * @return mixed
     */
    public function delete(Model $model);
}
