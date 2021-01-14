<?php

namespace Abr4xas\Plans\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanSubscriptionUsageModel extends Model
{

    use HasFactory;

    protected $table = 'plan_subscription_usages';

    protected $guarded = [];

    protected $fillable = ['subscription_id', 'code', 'used'];

    public function subscription()
    {
        return $this->belongsTo(config('plans.models.subscription'), 'subscription_id');
    }

    public function scopeCode($query, string $code)
    {
        return $query->where('code', $code);
    }
}
