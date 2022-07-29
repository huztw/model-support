<?php

namespace Huztw\ModelSupport\Facades;

use Illuminate\Support\Facades\Facade;

class ModelSupport extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'model-support';
    }
}
