<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        $record = $this->find($id);
        return $record->update($data);
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }
    public function getTwoConditions($field1, $value1, $field2, $value2)
    {
        return $this->model->where($field1, $value1)
            ->where($field2, $value2)
            ->get();
    }
    public function findByKey($field1, $value1)
    {
        return $this->model->where($field1, $value1)->first();
    }
}
