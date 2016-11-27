<?php
namespace Lightmedia\Googleprint;

use Lightmedia\Googleprint\Builders\PrinterBuilder;
use Lightmedia\Googleprint\Models\PrintJob;

class Googleprint {

    public static function printer() {

        return new PrinterBuilder();
    }

    public static function defaultPrinter() {

        return (new PrinterBuilder)->find(config('print.printers.default'));
    }

    public static function newPrintJob() {

        return new PrintJob;
    }
}
