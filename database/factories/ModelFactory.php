<?php

namespace Abr4xas\Plans\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class ModelFactory extends Factory
{
    protected $model = \Abr4xas\Plans\Models\PlanModel::class;

    public function definition()
    {
        return [
            'name' => 'Testing Plan '. \Illuminate\Support\Str::random(7),
            'description' => 'This is a testing plan.',
            'price' => (float) mt_rand(10, 200),
            'currency' => 'EUR',
            'duration' => 30,
        ];
    }
}
