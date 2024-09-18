<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Teams extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = ['name', 'logo', 'competition_id', 'country_id'];


    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competitions::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Countries::class);
    }
}
