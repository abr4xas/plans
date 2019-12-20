<?php

return [

    /*
     * The model which handles the plans tables.
     */

    'models' => [

        'plan' => \Abr4xas\Plans\Models\PlanModel::class,
        'subscription' => \Abr4xas\Plans\Models\PlanSubscriptionModel::class,
        'feature' => \Abr4xas\Plans\Models\PlanFeatureModel::class,
        'usage' => \Abr4xas\Plans\Models\PlanSubscriptionUsageModel::class,

        'stripeCustomer' => \Abr4xas\Plans\Models\StripeCustomerModel::class,

    ],
    /*
     * Payment methods
     */
    'payment_methods' => ['stripe'],

];
