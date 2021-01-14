<?php

namespace Abr4xas\Plans\Models;

use Abr4xas\Plans\Traits\ResolveClass;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanSubscriptionUsageModel extends Model
{
    use HasFactory;
    use ResolveClass;

    protected $table = 'plan_subscription_usages';

    protected $guarded = [];

    protected $fillable = [
        'subscription_id',
        'code',
        'used',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo($this->resolveClass('plans.models.subscription'), 'subscription_id');
    }

    public function scopeCode($query, string $code)
    {
        return $query->where('code', $code);
    }
}
