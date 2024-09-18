<?php

namespace App\Repositories;

use App\Models\Matches;
use Prettus\Repository\Eloquent\BaseRepository;

class MatchesRepositories extends BaseRepository
{
    public function boot() {}
    function model()
    {
        return Matches::class;
    }
}
