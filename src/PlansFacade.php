<?php

namespace Abr4xas\Plans;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Angel cruz\Plans\Plans
 */
class PlansFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'plans';
    }
}
