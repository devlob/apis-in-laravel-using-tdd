<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function create(string $model, array $attributes = [], $resource = true)
    {
        $resourceModel = factory("App\\$model")->create($attributes);
        $resourceClass = "App\\Http\Resources\\$model";

        if(!$resource)
            return $resourceModel;

        return new $resourceClass($resourceModel);
    }
}
