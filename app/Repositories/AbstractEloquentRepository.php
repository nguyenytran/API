<?php


namespace App\Repositories;


use App\Models\User;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractEloquentRepository implements BaseRepository
{
    /**
     * Name of the Model with absolute namespace
     *
     * @var string
     */
    protected $modelName;

    /**
     * Instance that extends Illuminate\Database\Eloquent\Model
     *
     * @var Model|Builder
     */
    protected $model;

    /**
     * get logged in user
     *
     * @var User $loggedInUser
     */
    protected $loggedInUser;

    /**
     * AbstractEloquentRepository constructor.
     *
     * @param Model|Builder $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->loggedInUser = $this->getLoggedInUser();
    }

    /**
     * Get Model instance
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @inheritdoc
     */
    public function findOne($id, array $relations = null)
    {
        $builder = null;
        if (!empty($relations)) {
            $builder = function (Builder $builder) use ($relations) {
                return $builder->with($relations);
            };
        }
        return $this->findOneBy(['id' => $id], $builder);
    }

    /**
     * @inheritdoc
     */
    public function findOneBy(array $criteria, \Closure $builder = null)
    {
        $queryBuilder = $this->model->where($criteria);
        if (is_callable($builder)) {
            $builder($queryBuilder);
        }
        return $queryBuilder->first();
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], \Closure $builder = null, $paginate = true)
    {
        $limit = !empty($searchCriteria['limit']) ? (int)$searchCriteria['limit'] : 15; // it's needed for pagination
        $filter = !empty($searchCriteria['filter']) ? (array)$searchCriteria['filter'] : [];
        $sort = !empty($searchCriteria['sort']) ? (string)$searchCriteria['sort'] : '';

        $queryBuilder = $this->model->where(function ($query) use ($filter, $sort) {
            $this->applySearchCriteriaInQueryBuilder($query, $filter);
        });

        $this->applySortingInQueryBuilder($queryBuilder, $sort);

        if (is_callable($builder)) {
            $builder($queryBuilder);
        }

        if ($paginate) {
            return $queryBuilder->paginate($limit);
        }

        return $queryBuilder->get();
    }


    /**
     * Apply condition on query builder based on search criteria
     *
     * @param Object $queryBuilder
     * @param array $searchCriteria
     * @return mixed
     */
    protected function applySearchCriteriaInQueryBuilder($queryBuilder, array $searchCriteria = [])
    {

        foreach ($searchCriteria as $key => $value) {

            // skip pagination related query params and ambiguous fields
            if (in_array($key, ['page', 'per_page', 'limit', 'deleted_at'])) {
                continue;
            }

            //we can pass multiple params for a filter with commas
            $allValues = explode(',', $value);

            if (count($allValues) > 1) {
                $queryBuilder->whereIn($key, $allValues);
            } else {
                $operator = '=';
                $queryBuilder->where($key, $operator, $value);
            }
        }

        return $queryBuilder;
    }

    /**
     * Apply condition on query builder based on search criteria
     *
     * @param \Illuminate\Database\Query\Builder|\Jenssegers\Mongodb\Eloquent\Builder $queryBuilder
     * @param string $sortString
     * @return mixed
     */
    protected function applySortingInQueryBuilder($queryBuilder, $sortString = null)
    {
        $sortFields = explode(',', $sortString);
        if (count($sortFields) > 0) {
            foreach ($sortFields as $field) {
                if (empty($field)) {
                    continue;
                }
                if (strpos($field, '-') === 0) {
                    $field = substr($field, 1);
                    if ($field) {
                        $queryBuilder->orderByDesc($field);
                    }
                } else {
                    $queryBuilder->orderBy($field);
                }
            }
        }

        return $queryBuilder;
    }

    /**
     * @inheritdoc
     */
    public function save(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data)
    {
        $fillAbleProperties = $this->model->getFillable();

        foreach ($data as $key => $value) {

            // update only fillAble properties
            if (in_array($key, $fillAbleProperties)) {
                $model->$key = $value;
            }
        }

        $model->save();

        // get updated model from database
        $model = $this->findOne($model->id);

        return $model;
    }

    /**
     * @inheritdoc
     */
    public function findIn($key, array $values)
    {
        return $this->model->whereIn($key, $values)->get();
    }

    /**
     * @param Model $model
     * @return bool|mixed|null
     * @throws \Exception
     */
    public function delete(Model $model)
    {
        return $model->delete();
    }

    /**
     * get loggedIn user
     *
     * @return User
     */
    protected function getLoggedInUser()
    {
        $user = user();

        if ($user instanceof User) {
            return $user;
        } else {
            return new User();
        }
    }
}
