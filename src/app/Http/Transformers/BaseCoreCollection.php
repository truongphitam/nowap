<?php

namespace App\Http\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseCoreCollection extends ResourceCollection
{
    protected function transformCollection()
    {
        return $this->collection;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'items' => $this->transformCollection(),
            'meta' => [
                'params' => $request->all(),
                'total' => $this->total() ?? null,
                'count' => $this->count() ?? null,
                'perPage' => $this->perPage() ?? null,
                'currentPage' => $this->currentPage() ?? null,
                'totalPages' => $this->lastPage() ?? null
            ],
        ];
    }
}