<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Competitions extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = ['name', 'logo'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string)Str::uuid();
            }
        });
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Teams::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(Matches::class, 'competition_id', 'id');
    }
}
