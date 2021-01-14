<?php

namespace Abr4xas\Plans\Events;

use Illuminate\Queue\SerializesModels;

class NewSubscription
{
    use SerializesModels;

    public \Illuminate\Database\Eloquent\Model $model;
    public \Abr4xas\Plans\Models\PlanSubscriptionModel $subscription;

    /**
     * @param \Illuminate\Database\Eloquent\Model $model The model that subscribed.
     * @param \Abr4xas\Plans\Models\PlanSubscriptionModel $subscription Subscription the model has subscribed to.
     * @return void
     */
    public function __construct($model, $subscription)
    {
        $this->model = $model;
        $this->subscription = $subscription;
    }
}
