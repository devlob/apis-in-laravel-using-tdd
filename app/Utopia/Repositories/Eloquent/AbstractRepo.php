<?php

namespace App\Utopia\Repositories\Eloquent;

use App\Utopia\Repositories\Interfaces\AbstractRepoInterface;

class AbstractRepo implements AbstractRepoInterface
{
    protected $model;

    public function __construct(string $model)
    {
        $this->model = "App\\$model";
    }

    public function findOrFail($id)
    {
        return $this->model::findOrfail($id);
    }

    public function paginate()
    {
        return $this->model::paginate();
    }
}
