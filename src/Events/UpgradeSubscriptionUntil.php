<?php

namespace Abr4xas\Plans\Events;

use Illuminate\Queue\SerializesModels;

class UpgradeSubscriptionUntil
{
    use SerializesModels;

    public \Illuminate\Database\Eloquent\Model $model;
    public \Abr4xas\Plans\Models\PlanSubscriptionModel $subscription;
    public \Carbon\Carbon $expiresOn;
    public bool $startFromNow;
    public \Abr4xas\Plans\Models\PlanModel $oldPlan;
    public \Abr4xas\Plans\Models\PlanModel $newPlan;

    /**
     * @param \Illuminate\Database\Eloquent\Model $model The model on which the action was done.
     * @param \Abr4xas\Plans\Models\PlanSubscriptionModel $subscription Subscription that was upgraded.
     * @param \Carbon\Carbon $expiresOn The date when the upgraded subscription expires.
     * @param bool $startFromNow Wether the current subscription is upgraded by extending now or is upgraded at the next cycle.
     * @param null|\Abr4xas\Plans\Models\PlanModel $oldPlan The old plan.
     * @param null|\Abr4xas\Plans\Models\PlanModel $newPlan The new plan.
     * @psalm-suppress PossiblyNullPropertyAssignmentValue
     * @return void
     */
    public function __construct($model, $subscription, $expiresOn, bool $startFromNow, $oldPlan, $newPlan)
    {
        $this->model = $model;
        $this->subscription = $subscription;
        $this->expiresOn = $expiresOn;
        $this->startFromNow = $startFromNow;
        $this->oldPlan = $oldPlan;
        $this->newPlan = $newPlan;
    }
}
