<?php namespace Proximo\Facades;

use Illuminate\Support\Facades;

class Manager extends \Illuminate\Support\Facades\Facade {

    protected static function getFacadeAccessor() { return 'proximo.manager'; }

}

