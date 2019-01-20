<?php

namespace App\Utopia\Repositories\Interfaces;

interface AbstractRepoInterface
{
    public function findOrFail($id);

    public function paginate();
}
