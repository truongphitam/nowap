<?php

namespace App\Http\Transformers;

use App\Http\Resources\CompetitionResource;

class CompetitionCollection extends BaseCoreCollection
{
    protected function transformCollection()
    {
        return CompetitionResource::collection(parent::transformCollection());
    }
}