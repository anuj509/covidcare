<?php

namespace App\Repositories;

use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Model;


abstract class BaseRepository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Application
     */
    protected $app;
    protected $with = [];
    /**
     * @param Application $app
     *
     * @throws \Exception
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * Get searchable fields array
     *
     * @return array
     */
    abstract public function getFieldsSearchable();

    /**
     * Configure the Model
     *
     * @return string
     */
    abstract public function model();

    /**
     * Make Model instance
     *
     * @throws \Exception
     *
     * @return Model
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * Paginate records for scaffold.
     *
     * @param int $perPage
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage, $columns = ['*'])
    {
        $query = $this->allQuery();

        return $query->paginate($perPage, $columns);
    }

    /**
     * Build a query for retrieving all records.
     *
     * @param array $search
     * @param int|null $skip
     * @param int|null $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function allQuery($search = [], $skip = null, $limit = null)
    {
        $query = $this->model->newQuery()->with($this->with);
        if (count($search)) {
            foreach($search as $key => $value) {
                if (in_array($key, $this->getFieldsSearchable())) {
                    $query->where($key, $value);
                }
            }
        }

        if (!is_null($skip)) {
            $query->skip($skip);
        }

        if (!is_null($limit)) {
            $query->limit($limit);
        }

        return $query;
    }

    /**
     * Retrieve all records with given filter criteria
     *
     * @param array $search
     * @param int|null $skip
     * @param int|null $limit
     * @param array $columns
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function all($search = [], $skip = null, $limit = null, $columns = ['*'])
    {
        $query = $this->allQuery($search, $skip, $limit);

        return $query->get($columns);
    }

    /**
     * Create model record
     *
     * @param array $input
     *
     * @return Model
     */
    public function create($input)
    {
        $model = $this->model->newInstance($input);

        $model->save();

        return $model;
    }

    /**
     * Find model record for given id
     *
     * @param int $id
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model|null
     */
    public function find($id, $columns = ['*'])
    {
        $query = $this->model->newQuery()->with($this->with);

        return $query->find($id, $columns);
    }

    /**
     * Update model record for given id
     *
     * @param array $input
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model
     */
    public function update($input, $id)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        $model->fill($input);

        $model->save();

        return $model;
    }

    /**
     * @param int $id
     *
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function delete($id)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        return $model->delete();
    }

    public function latest()
    {
        $query = $this->model->newQuery();

        return $query->latest()->first();
    }

    public function with($relations)
    {

        if (is_string($relations))
        {
            $this->with = explode(',', $relations);

            return $this;
        }

        $this->with = is_array($relations) ? $relations : [];

        return $this;
    }

    public function findBy($search, $skip = null, $limit = null) {
        $query = $this->model->newQuery();
        foreach($search as $key => $value) {
            if (in_array($key, $this->getFieldsSearchable())) {
                $exp = "/like([%])\w+([%])/";
                if(preg_match($exp, $value)==1){
                    // dd(preg_match($exp, $value));
                    $value = str_replace($value,'like%','');
                    $value = str_replace($value,'%','');
                    $query->where($key, 'LIKE', '%'.$value.'%');
                }else{
                    $query->where($key, $value);
                }
            }
        }

        if (!is_null($skip)) {
            $query->skip($skip);
        }

        if (!is_null($limit)) {
            $query->limit($limit);
        }
        return $query;
    }

    // public function findByOr($search, $optionals, $skip = null, $limit = null) {
    //     $query = $this->model->newQuery();
    //     $exp = "/like([%])\w+([%])/";
    //     foreach($search as $key => $value) {
    //         if (in_array($key, $this->getFieldsSearchable())) {
    //             if(preg_match($exp, $value)==1){
    //                 // dd(preg_match($exp, $value));
    //                 $value = str_replace($value,'like%','');
    //                 $value = str_replace($value,'%','');
    //                 $query->where($key, 'LIKE', '%'.$value.'%');
    //             }else{
    //                 $query->where($key, $value);
    //             }
    //         }
    //     }
    //     // foreach($optionals as $key => $value) {
    //     //     if (in_array($key, $this->getFieldsSearchable())) {
    //     //         if($key==array_key_first($optionals)){
    //     //             if(preg_match($exp, $value)==1){
    //     //                 // dd(preg_match($exp, $value));
    //     //                 $value = str_replace($value,'like%','');
    //     //                 $value = str_replace($value,'%','');
    //     //                 $query->where($key, 'LIKE', '%'.$value.'%');
    //     //             }else{
    //     //                 $query->where($key, $value);
    //     //             }
    //     //         }else{
    //     //             // dd($key);
    //     //             // $query->orWhere($key, $value);
    //     //             if(preg_match($exp, $value)==1){
    //     //                 // dd(preg_match($exp, $value));
    //     //                 $value = str_replace($value,'like%','');
    //     //                 $value = str_replace($value,'%','');
    //     //                 $query->orWhere($key, 'LIKE', '%'.$value.'%');
    //     //             }else{
    //     //                 $query->orWhere($key, $value);
    //     //             }
    //     //         }
    //     //     }
    //     // }

    //     if (!is_null($skip)) {
    //         $query->skip($skip);
    //     }

    //     if (!is_null($limit)) {
    //         $query->limit($limit);
    //     }
    //     return $query;
    // }
}
