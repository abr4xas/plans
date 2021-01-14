<?php

namespace Abr4xas\Plans\Models;

use Abr4xas\Plans\Traits\ResolveClass;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanFeatureModel extends Model
{
    use HasFactory;
    use ResolveClass;

    protected $table = 'plan_features';

    protected $guarded = [];

    protected $fillable = [
        'plan_id',
        'name',
        'code',
        'description',
        'type',
        'limit',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'object',
    ];

    /** @psalm-suppress PossiblyInvalidCast */
    public function plan(): BelongsTo
    {
        return $this->belongsTo($this->resolveClass('plans.models.plan'), 'plan_id');
    }

    /**
     * Undocumented function
     *
     * @psalm-suppress MissingParamType
     * @psalm-suppress MissingReturnType
     */
    public function scopeCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Undocumented function
     *
     * @psalm-suppress MissingParamType
     * @psalm-suppress MissingReturnType
     */
    public function scopeLimited($query)
    {
        return $query->where('type', 'limit');
    }

    /**
     * Undocumented function
     *
     * @psalm-suppress MissingParamType
     * @psalm-suppress MissingReturnType
     */
    public function scopeFeature($query)
    {
        return $query->where('type', 'feature');
    }

    public function isUnlimited(): bool
    {
        return ($this->type == 'limit' && $this->limit < 0);
    }
}
