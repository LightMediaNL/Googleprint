<?php
namespace Lightmedia\Googleprint;

use Lightmedia\Googleprint\Builders\PrinterBuilder;

class Googleprint {

    public static function printer() {

        return new PrinterBuilder();
    }
}
