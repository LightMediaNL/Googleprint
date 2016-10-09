<?php
namespace Lightmedia\Googleprint\Models;

use Lightmedia\Googleprint\Exceptions\GooglePrintException;

class BaseObject{

    public function __construct($id = null) {

        $this->id          = $id;
    }

    public function __get($name) {

        if(false === in_array($name, $this->visible)) {

            throw new GooglePrintException('Value ' . $name . ' is not available');
        }

        if(false === isset( $this->values[$name] ) && false === $this->isFullObject) {
            $this->getFullObject();
        }

        $methodName = $this->getMethodName($name);

        if(true === method_exists($this, $methodName)) {

            return $this->$methodName();

        } else if(true === isset( $this->values[$name] )) {

            return $this->values[$name];

        } else {

            return null;
        }
    }

    public function __set($name, $value) {

        if(false === in_array($name, $this->fillable)) {
            throw new GooglePrintException('It is not allowed to set ' . $name . ' on ' . $this->getClassName());
        }

        $methodName = $this->getSetMethodName($name);
        if(true === method_exists($this, $methodName)) {
            $this->$methodName($value);
        } else {
            $this->$name = $value;
        }
    }

    protected function getClassName() {

        return str_replace('Lightmedianl\\Googleprint\\Models\\', '', get_called_class());
    }

}