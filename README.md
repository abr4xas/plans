# plans

[![Latest Version on Packagist](https://img.shields.io/packagist/v/abr4xas/plans.svg?style=flat-square)](https://packagist.org/packages/abr4xas/plans)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/abr4xas/plans/run-tests?label=tests)](https://github.com/abr4xas/plans/actions?query=workflow%3ATests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/abr4xas/plans.svg?style=flat-square)](https://packagist.org/packages/abr4xas/plans)


Laravel Plans is a package for SaaS apps that need management over plans, features, subscriptions, events for plans or limited, countable features.

## Installation

You can install the package via composer:

```bash
composer require angel cruz/plans
```


If your Laravel version does not support package discovery, add this line in the `providers` array in your `config/app.php` file:

```php
Abr4xas\Plans\PlansServiceProvider::class,
```


You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Angel cruz\Plans\PlansServiceProvider" --tag="migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Angel cruz\Plans\PlansServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
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
    ],

];
```

## Usage

Migrate the database:
```bash
$ php artisan migrate
```

Add the `HasPlans` trait to your Eloquent model:
```php
use Abr4xas\Plans\Traits\HasPlans;

class User extends Model {
    use HasPlans;
    ...
}
```

# Creating plans
The basic unit of the subscription-like system is a plan. You can create it using `Abr4xas\Plans\Models\PlanModel` or your model, if you have implemented your own.

```php
$plan = PlanModel::create([
    'name' => 'Enterprise',
    'description' => 'The biggest plans of all.',
    'price' => 20.99,
    'currency' => 'EUR',
    'duration' => 30, // in days
    'metadata' => ['key1' => 'value1', ...],
]);
```

# Features
Each plan has features. They can be either countable, and those are limited or unlimited, or there just to store the information, such a specific permission.

Marking a feature type can be done using:
* `feature`, is a single string, that do not needs counting. For example, you can store permissions.
* `limit`, is a number. For this kind of feature, the `limit` attribute will be filled. It is meant to measure how many of that feature the user has consumed, from this subscription. For example, you can count how many build minutes this user has consumed during the month (or during the Cycle, which is 30 days in this example)

**Note: For unlimited feature, the `limit` field will be set to any negative value.**

To attach features to your plan, you can use the relationship `features()` and pass as many `Abr4xas\Plans\Models\PlanFeatureModel`instances as you need:
```php
$plan->features()->saveMany([
    new PlanFeatureModel([
        'name' => 'Vault access',
        'code' => 'vault.access',
        'description' => 'Offering access to the vault.',
        'type' => 'feature',
        'metadata' => ['key1' => 'value1', ...],
    ]),
    new PlanFeatureModel([
        'name' => 'Build minutes',
        'code' => 'build.minutes',
        'description' => 'Build minutes used for CI/CD.',
        'type' => 'limit',
        'limit' => 2000,
        'metadata' => ['key1' => 'value1', ...],
    ]),
    new PlanFeatureModel([
        'name' => 'Users amount',
        'code' => 'users.amount',
        'description' => 'The maximum amount of users that can use the app at the same time.',
        'type' => 'limit',
        'limit' => -1, // or any negative value
        'metadata' => ['key1' => 'value1', ...],
    ]),
    ...
]);
```

Later, you can retrieve the permissions directly from the subscription:
```php
$subscription->features()->get(); // All features
$subscription->features()->code($codeId)->first(); // Feature with a specific code.
$subscription->features()->limited()->get(); // Only countable/unlimited features.
$subscription->features()->feature()->get(); // Uncountable, permission-like features.
```

# Subscribing to plans
Your users can be subscribed to plans for a certain amount of days or until a certain date.
```php
$subscription = $user->subscribeTo($plan, 30); // 30 days
$subscription->remainingDays(); // 29 (29 days, 23 hours, ...)
```

By default, the plan is marked as `recurring`, so it's eligible to be extended after it expires, if you plan to do so like it's explained in the **Recurrency** section below.

If you don't want a recurrent subscription, you can pass `false` as a third argument:
```php
$subscription = $user->subscribeTo($plan, 30, false); // 30 days, non-recurrent
```

If you plan to subscribe your users until a certain date, you can pass strngs containing a date, a datetime or a Carbon instance.

If your subscription is recurrent, the amount of days for a recurrency cycle is the difference between the expiring date and the current date.
```php
$user->subscribeToUntil($plan, '2018-12-21');
$user->subscribeToUntil($plan, '2018-12-21 16:54:11');
$user->subscribeToUntil($plan, Carbon::create(2018, 12, 21, 16, 54, 11));

$user->subscribeToUntil($plan, '2018-12-21', false); // no recurrency
```

**Note: If the user is already subscribed, the `subscribeTo()` will return false. To avoid this, upgrade or extend the subscription.**

# Upgrading subscription

Upgrading the current subscription's plan can be done in two ways: it either extends the current subscription with the amount of days passed or creates another one, in extension to this current one.

Either way, you have to pass a boolean as the third parameter. By default, it extends the current subscription.
```php
// The current subscription got longer with 60 days.
$currentSubscription = $user->upgradeCurrentPlanTo($anotherPlan, 60, true);

// A new subscription, with 60 days valability, starting when the current one ends.
$newSubscription = $user->upgradeCurrentPlanTo($anotherPlan, 60, false);
```

Just like the subscribe methods, upgrading also support dates as a third parameter if you plan to create a new subscription at the end of the current one.
```php
$user->upgradeCurrentPlanToUntil($anotherPlan, '2018-12-21', false);
$user->upgradeCurrentPlanToUntil($anotherPlan, '2018-12-21 16:54:11', false);
$user->upgradeCurrentPlanToUntil($anotherPlan, Carbon::create(2018, 12, 21, 16, 54, 11), false);
```

Passing a fourth parameter is available, if your third parameter is `false`, and you should pass it if you'd like to mark the new subscription as recurring.
```php
// Creates a new subscription that starts at the end of the current one, for 30 days and recurrent.
$newSubscription = $user->upgradeCurrentPlanTo($anotherPlan, 30, false, true);
```

# Extending current subscription
Upgrading uses the extension methods, so it uses the same arguments, but you do not pass as the first argument a Plan model:
```php
// The current subscription got extended with 60 days.
$currentSubscription = $user->extendCurrentSubscriptionWith(60, true);

// A new subscription, which starts at the end of the current one.
$newSubscrioption = $user->extendCurrentSubscriptionWith(60, false);

// A new subscription, which starts at the end of the current one and is recurring.
$newSubscrioption = $user->extendCurrentSubscriptionWith(60, false, true);
```

Extending also works with dates:
```php
$user->extendCurrentSubscriptionUntil('2018-12-21');
```

# Cancelling subscriptions
You can cancel subscriptions. If a subscription is not finished yet (it is not expired), it will be marked as `pending cancellation`. It will be fully cancelled when the expiration dates passes the current time and is still cancelled.
```php
// Returns false if there is not an active subscription.
$user->cancelCurrentSubscription();
$lastActiveSubscription = $user->lastActiveSubscription();

$lastActiveSubscription->isCancelled(); // true
$lastActiveSubscription->isPendingCancellation(); // true
$lastActiveSubscription->isActive(); // false

$lastActiveSubscription->hasStarted();
$lastActiveSubscription->hasExpired();
```

# Consuming countable features
To consume the `limit` type feature, you have to call the `consumeFeature()` method within a subscription instance.

To retrieve a subscription instance, you can call `activeSubscription()` method within the user that implements the trait. As a pre-check, don't forget to call `hasActiveSubscription()` from the user instance to make sure it is subscribed to it.

```php
if ($user->hasActiveSubscription()) {
    $subscription = $user->activeSubscription();
    $subscription->consumeFeature('build.minutes', 10);

    $subscription->getUsageOf('build.minutes'); // 10
    $subscription->getRemainingOf('build.minutes'); // 1990
}
```

The `consumeFeature()` method will return:
* `false` if the feature does not exist, the feature is not a `limit` or the amount is exceeding the current feature allowance
* `true` if the consumption was done successfully

```php
// Note: The remaining of build.minutes is now 1990

$subscription->consumeFeature('build.minutes', 1991); // false
$subscription->consumeFeature('build.hours', 1); // false
$subscription->consumeFeature('build.minutes', 30); // true

$subscription->getUsageOf('build.minutes'); // 40
$subscription->getRemainingOf('build.minutes'); // 1960
```

If `consumeFeature()` meets an unlimited feature, it will consume it and it will also track usage just like a normal record in the database, but will never return false. The remaining will always be `-1` for unlimited features.

The revering method for `consumeFeature()` method is `unconsumeFeature()`. This works just the same, but in the reverse:
```php
// Note: The remaining of build.minutes is 1960

$subscription->consumeFeature('build.minutes', 60); // true

$subscription->getUsageOf('build.minutes'); // 100
$subscription->getRemainingOf('build.minutes'); // 1900

$subscription->unconsumeFeature('build.minutes', 100); // true
$subscription->unconsumeFeature('build.hours', 1); // false

$subscription->getUsageOf('build.minutes'); // 0
$subscription->getRemainingOf('build.minutes'); // 2000
```

Using the `unconsumeFeature()` method on unlimited features will also reduce usage, but it will never reach negative values.


# Recurrency
This package doesn't support what Cashier supports: Stripe Plans & Stripe Coupons.
This package is able to make you the master, without using a third party to handle subscriptions and recurrency. The main advantage is that you can define your own recurrency amount of days, while Stripe is limited to daily, weekly, monthly and yearly.

To handle recurrency, there is a method called `renewSubscription` that does the job for you. You will have to loop through all your subscribers.
Preferably, you should run a cron command that will call the method on each subscriber.

This method will renew (if needed) the subscription for the user.
```php
foreach(User::all() as $user) {
    $user->renewSubscription();
}
```

If you use the integrated Stripe Charge feature, you will have to pass a Stripe Token to charge from that user. Since Stripe Tokens are disposable (one-time use), you will have to manage getting a token from your users.
```php
$user->renewSubscription('tok...');
```

As always, if the payment was processed, it will fire the `Abr4xas\Plans\Stripe\ChargeSuccessful` event, or if the payment failed, it will fire `Abr4xas\Plans\Stripe\ChargeFailed` event.

# Due subscriptions
Subscriptions that are not using the local Stripe Charge feature will never be marked as `Due` since all of them are paid, by default.

If your app uses your own payment method, you can pass a closure for the following `chargeForLastDueSubscription()` method that will help you get control over the due subscription:
```php
$user->chargeForLastDueSubscription(function($subscription) {
    // process the payment here

    if($paymentSuccessful) {
        $subscription->update([
            'is_paid' => true,
            'starts_on' => Carbon::now(),
            'expires_on' => Carbon::now()->addDays($subscription->recurring_each_days),
        ]);

        return $subscription;
    }

    return null;
});
```

On failed payment, they are marked as Due. They need to be paid, and each action like subscribing, upgrading or extending will always try to re-pay the subscription by deleting the last one, creating the one intended in one of the actions mentioned and trying to pay it.

To do so, `chargeForLastDueSubscription()` will help you charge the user for the last, unpaid subscription. You will have to explicitly pass a Stripe Token for this:
```php
$user->withStripe()->withStripeToken('tok_...')->chargeForLastDueSubscription();
```

For this method, `\Abr4xas\Plans\Events\Stripe\DueSubscriptionChargeSuccess` and `\Abr4xas\Plans\Events\Stripe\DueSubscriptionChargeFailed` are thrown on succesful charge or failed charge.

# Model Extends

You can extend Plan models as well

**note** `$table`, `$fillable`, `$cast`, `Relationships` will be inherit

## PlanModel
```php
<?php
namespace App\Models;
use Abr4xas\Plans\Models\PlanModel;
class Plan extends PlanModel {
    //
}
```

## PlanFeatureModel
```php
<?php
namespace App\Models;
use Abr4xas\Plans\Models\PlanFeatureModel;
class PlanFeature extends PlanFeatureModel {
    //
}
```

## PlanSubscriptionModel
```php
<?php
namespace App\Models;
use Abr4xas\Plans\Models\PlanSubscriptionModel;
class PlanSubscription extends PlanSubscriptionModel {
    //
}
```

## PlanSubscriptionUsageModel
```php
<?php
namespace App\Models;
use Abr4xas\Plans\Models\PlanSubscriptionUsageModel;
class PlanSubscriptionUsage extends PlanSubscriptionUsageModel {
    //
}
```

## StripteCustomerModel
```php
<?php
namespace App\Models;
use Abr4xas\Plans\Models\StripteCustomerModel;
class StripeCustomer extends StripteCustomerModel {
    //
}
```

# Events
When using subscription plans, you want to listen for events to automatically run code that might do changes for your app.

Events are easy to use. If you are not familiar, you can check [Laravel's Official Documentation on Events](https://laravel.com/docs/5.6/events).

All you have to do is to implement the following Events in your `EventServiceProvider.php` file. Each event will have it's own members than can be accessed through the `$event` variable within the `handle()` method in your listener.

```php
$listen = [
    ...
    \Abr4xas\Plans\Events\CancelSubscription::class => [
        // $event->model = The model that cancelled the subscription.
        // $event->subscription = The subscription that was cancelled.
    ],
    \Abr4xas\Plans\Events\NewSubscription::class => [
        // $event->model = The model that was subscribed.
        // $event->subscription = The subscription that was created.
    ],
     \Abr4xas\Plans\Events\NewSubscriptionUntil::class => [
        // $event->model = The model that was subscribed.
        // $event->subscription = The subscription that was created.
    ],
    \Abr4xas\Plans\Events\ExtendSubscription::class => [
        // $event->model = The model that extended the subscription.
        // $event->subscription = The subscription that was extended.
        // $event->startFromNow = If the subscription is exteded now or is created a new subscription, in the future.
        // $event->newSubscription = If the startFromNow is false, here will be sent the new subscription that starts after the current one ends.
    ],
    \Abr4xas\Plans\Events\ExtendSubscriptionUntil::class => [
        // $event->model = The model that extended the subscription.
        // $event->subscription = The subscription that was extended.
        // $event->expiresOn = The Carbon instance of the date when the subscription will expire.
        // $event->startFromNow = If the subscription is exteded now or is created a new subscription, in the future.
        // $event->newSubscription = If the startFromNow is false, here will be sent the new subscription that starts after the current one ends.
    ],
    \Abr4xas\Plans\Events\UpgradeSubscription::class => [
        // $event->model = The model that upgraded the subscription.
        // $event->subscription = The current subscription.
        // $event->startFromNow = If the subscription is upgraded now or is created a new subscription, in the future.
        // $event->oldPlan = Here lies the current (which is now old) plan.
        // $event->newPlan = Here lies the new plan. If it's the same plan, it will match with the $event->oldPlan
    ],
    \Abr4xas\Plans\Events\UpgradeSubscriptionUntil::class => [
        // $event->model = The model that upgraded the subscription.
        // $event->subscription = The current subscription.
        // $event->expiresOn = The Carbon instance of the date when the subscription will expire.
        // $event->startFromNow = If the subscription is upgraded now or is created a new subscription, in the future.
        // $event->oldPlan = Here lies the current (which is now old) plan.
        // $event->newPlan = Here lies the new plan. If it's the same plan, it will match with the $event->oldPlan
    ],
    \Abr4xas\Plans\Events\FeatureConsumed::class => [
        // $event->subscription = The current subscription.
        // $event->feature = The feature that was used.
        // $event->used = The amount used.
        // $event->remaining = The total amount remaining. If the feature is unlimited, will return -1
    ],
     \Abr4xas\Plans\Events\FeatureUnconsumed::class => [
        // $event->subscription = The current subscription.
        // $event->feature = The feature that was used.
        // $event->used = The amount reverted.
        // $event->remaining = The total amount remaining. If the feature is unlimited, will return -1
    ],
    \Abr4xas\Plans\Events\Stripe\ChargeFailed::class => [
        // $event->model = The model for which the payment failed.
        // $event->subscription = The subscription.
        // $event->exception = The exception thrown by the Stripe API wrapper.
    ],
    \Abr4xas\Plans\Events\Stripe\ChargeSuccessful::class => [
        // $event->model = The model for which the payment succeded.
        // $event->subscription = The subscription which was updated as paid.
        // $event->stripeCharge = The response coming from the Stripe API wrapper.
    ],
    \Abr4xas\Plans\Events\Stripe\DueSubscriptionChargeFailed::class => [
        // $event->model = The model for which the payment failed.
        // $event->subscription = The due subscription that cannot be paid.
        // $event->exception = The exception thrown by the Stripe API wrapper.
    ],
    \Abr4xas\Plans\Events\Stripe\DueSubscriptionChargeSuccess::class => [
        // $event->model = The model for which the payment succeded.
        // $event->subscription = The due subscription that was paid.
        // $event->stripeCharge = The response coming from the Stripe API wrapper.
    ],
];
```


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Credits

- [angel cruz](https://github.com/abr4xas)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
