<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Matches extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'competition_id',
        'home_team_id',
        'away_team_id',
        'status_id',
        'match_time',
        'home_scores',
        'away_scores'
    ];

    protected $casts = [
        'home_scores' => 'array',
        'away_scores' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string)Str::uuid();
            }
        });
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competitions::class, 'competition_id', 'id');
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Teams::class, 'home_team_id', 'id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Teams::class, 'away_team_id', 'id');
    }
}
