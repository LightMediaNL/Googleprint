<?php
namespace Lightmedia\Googleprint\Facades;

use Illuminate\Support\Facades\Facade;

class GooglePrint extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'Googleprint'; }
}