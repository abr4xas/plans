<?php

namespace Abr4xas\Plans\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanFeatureModel extends Model
{
    use HasFactory;

    protected $table = 'plan_features';

    protected $guarded = [];

    protected $fillable = ['plan_id', 'name', 'code', 'description', 'type', 'limit', 'metadata'];

    protected $casts = [
        'metadata' => 'object',
    ];

    public function plan()
    {
        return $this->belongsTo(config('plans.models.plan'), 'plan_id');
    }

    public function scopeCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    public function scopeLimited($query)
    {
        return $query->where('type', 'limit');
    }

    public function scopeFeature($query)
    {
        return $query->where('type', 'feature');
    }

    public function isUnlimited()
    {
        return ($this->type == 'limit' && $this->limit < 0);
    }
}
