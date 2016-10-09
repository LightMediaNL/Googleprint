<?php
namespace Lightmedia\Googleprint\Cache;

use Cache;
use Lightmedia\Googleprint\Builders\QueryBuilder;

class GooglePrintCache extends Cache {

    protected $driver = null;
    protected $tags = null;

    const PREFIX = 'googlePrint.';

    public static function __callStatic($name, $arguments) {

        $arguments[0] = self::PREFIX . $arguments[0];

        return self::cache()->$name(... $arguments);
    }

    protected static function cache() {
        
        $request = new QueryBuilder;

        return Cache::store(config('print.cache.driver'));
    }
}