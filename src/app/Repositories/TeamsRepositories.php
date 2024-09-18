<?php

namespace App\Repositories;

use App\Models\Teams;
use Prettus\Repository\Eloquent\BaseRepository;

class TeamsRepositories extends BaseRepository
{
    public function boot() {}
    function model()
    {
        return Teams::class;
    }
}
