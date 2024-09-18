<?php

namespace App\Repositories;

use App\Models\Countries;
use Prettus\Repository\Eloquent\BaseRepository;

class CountriesRepositories extends BaseRepository
{
    public function boot() {}
    function model()
    {
        return Countries::class;
    }
}
