<?php

namespace Abr4xas\Plans\Test\Models;

use Abr4xas\Plans\Traits\HasPlans;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasPlans;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
