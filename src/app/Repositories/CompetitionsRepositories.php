<?php

namespace App\Repositories;

use App\Models\Competitions;
use Prettus\Repository\Eloquent\BaseRepository;

class CompetitionsRepositories extends BaseRepository
{
    public function boot() {}
    function model()
    {
        return Competitions::class;
    }
}
