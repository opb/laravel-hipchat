<?php namespace Opb\LaravelHipchat\Facades;

use Illuminate\Support\Facades\Facade;

class HipchatNotifier extends Facade{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'hipchat-notifier'; }

} 