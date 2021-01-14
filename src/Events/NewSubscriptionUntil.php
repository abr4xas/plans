<?php

namespace Abr4xas\Plans\Events;

use Illuminate\Queue\SerializesModels;

class NewSubscriptionUntil
{
    use SerializesModels;

    public \Illuminate\Database\Eloquent\Model $model;
    public \Abr4xas\Plans\Models\PlanSubscriptionModel $subscription;
    public \Carbon\Carbon $expiresOn;

    /**
     * @param \Illuminate\Database\Eloquent\Model  $model The model that subscribed.
     * @param \Abr4xas\Plans\Models\PlanSubscriptionModel $subscription Subscription the model has subscribed to.
     * @param \Carbon\Carbon $expiresOn The date when the subscription expires.
     * @return void
     */
    public function __construct($model, $subscription, $expiresOn)
    {
        $this->model = $model;
        $this->subscription = $subscription;
        $this->expiresOn = $expiresOn;
    }
}
