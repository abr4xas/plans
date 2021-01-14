<?php

namespace Abr4xas\Plans\Events;

use Illuminate\Queue\SerializesModels;

class FeatureUnconsumed
{
    use SerializesModels;

    public \Abr4xas\Plans\Models\PlanSubscriptionModel $subscription;
    public \Abr4xas\Plans\Models\PlanFeatureModel $feature;
    public float $used;
    public float $remaining;

    /**
     * @param \Abr4xas\Plans\Models\PlanSubscriptionModel $subscription Subscription on which action was done.
     * @param \Abr4xas\Plans\Models\PlanFeatureModel $feature The feature that was consumed.
     * @param float $used The amount used on this unconsumption.
     * @param float $remaining The amount remaining for this feature.
     * @return void
     */
    public function __construct($subscription, $feature, float $used, float $remaining)
    {
        $this->subscription = $subscription;
        $this->feature = $feature;
        $this->used = $used;
        $this->remaining = $remaining;
    }
}
