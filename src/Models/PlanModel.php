<?php

namespace Abr4xas\Plans\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanModel extends Model
{

    use HasFactory;

    protected $table = 'plans';

    protected $guarded = [];

    protected $fillable = ['name', 'description', 'price', 'currency', 'duration', 'metadata'];

    protected $casts = [
        'metadata' => 'object',
    ];

    public function features()
    {
        return $this->hasMany(config('plans.models.feature'), 'plan_id');
    }
}
