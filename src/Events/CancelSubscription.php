<?php

namespace Abr4xas\Plans\Events;

use Illuminate\Queue\SerializesModels;

class CancelSubscription
{
    use SerializesModels;

    public \Illuminate\Database\Eloquent\Model $model;
    public \Abr4xas\Plans\Models\PlanSubscriptionModel $subscription;

    /**
     * @param \Illuminate\Database\Eloquent\Model $model The model on which the action was done.
     * @param \Abr4xas\Plans\Models\PlanSubscriptionModel $subscription Subscription that was cancelled.
     * @return void
     */
    public function __construct($model, $subscription)
    {
        $this->model = $model;
        $this->subscription = $subscription;
    }
}
