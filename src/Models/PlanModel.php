<?php

namespace Abr4xas\Plans\Models;

use Abr4xas\Plans\Traits\ResolveClass;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanModel extends Model
{
    use HasFactory;
    use ResolveClass;

    protected $table = 'plans';

    protected $guarded = [];

    protected $fillable = [
        'name',
        'description',
        'price',
        'currency',
        'duration',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'object',
    ];

    public function features(): HasMany
    {
        return $this->hasMany($this->resolveClass('plans.models.feature'), 'plan_id');
    }
}
