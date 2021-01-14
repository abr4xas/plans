<?php

namespace Abr4xas\Plans\Events;

use Illuminate\Queue\SerializesModels;

class ExtendSubscriptionUntil
{
    use SerializesModels;

    public \Illuminate\Database\Eloquent\Model $model;
    public \Abr4xas\Plans\Models\PlanSubscriptionModel $subscription;
    public \Carbon\Carbon $expiresOn;
    public bool $startFromNow;
    public \Abr4xas\Plans\Models\PlanSubscriptionModel $newSubscription;

    /**
     * @param \Illuminate\Database\Eloquent\Model $model The model on which the action was done.
     * @param \Abr4xas\Plans\Models\PlanSubscriptionModel $subscription Subscription that was extended.
     * @param \Carbon\Carbon $expiresOn The date when the subscription expires.
     * @param bool $startFromNow Wether the current subscription is extended or is created at the next cycle.
     * @param null|\Abr4xas\Plans\Models\PlanSubscriptionModel $newSubscription Null if $startFromNow is true; The new subscription created in extension.
     * @psalm-suppress PossiblyNullPropertyAssignmentValue
     * @return void
     */
    public function __construct($model, $subscription, $expiresOn, bool $startFromNow, $newSubscription)
    {
        $this->model = $model;
        $this->subscription = $subscription;
        $this->expiresOn = $expiresOn;
        $this->startFromNow = $startFromNow;
        $this->newSubscription = $newSubscription;
    }
}
