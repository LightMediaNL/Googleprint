<?php
namespace Lightmedia\Googleprint\Models;

use Lightmedia\Googleprint\Exceptions\GooglePrintException;
use Lightmedia\Googleprint\Models\PrintJob;

class Printer extends BaseObject{

    protected $id;
    protected $isFullObject;
    protected $values = [ ];
    protected $fillable = [];

    protected $visible = [ 'status', 'id', 'name', 'ownerId', 'ownerName', 'description', 'state', 'location'];

    protected function getMethodName($name) {

        return 'get' . ucfirst(camel_case($name)) . 'Attribute';
    }

    protected function getFullObject() {


    }

    public function submit(PrintJob $job) {

        $this->canPrint();

        return $job->printer($this)->save();
    }

    protected function getStatusAttribute() {

        if(true === isset( $this->values['connectionStatus'] )) {

            return strtolower($this->values['connectionStatus']);
        } else {

            return null;
        }
    }

    protected function getLocationAttribute() {

        return $this->getTag('__cp__printer-location');
    }

    public function getStateAttribute() {

        if(true === isset( $this->values['uiState'] ) && true === isset( $this->values['uiState']['summary'] )) {
            return strtolower($this->values['uiState']['summary']);
        } else {
            return null;
        }
    }

    protected function getNameAttribute() {

        if(false === empty( $this->values['displayName'] )) {

            return $this->values['displayName'];

        } else if(false === empty( $this->values['name'] )) {

            return $this->values['name'];

        } else if(false === empty( $this->values['defaultDisplayName'] )) {

            return $this->values['defaultDisplayName'];

        } else {

            return null;
        }
    }
    
    protected function canPrint() {

        if(true === $this->values['isTosAccepted']) {
            throw new GooglePrintException('Please go to the Google Cloud Print website and accept Terms of Service');
        }
    }

    public function assign(array $array) {

        $this->values = array_merge_recursive($this->values, $array);

        if(true === isset( $this->values['capabilities'] )) {
            $this->isFullObject = true;
        }

        return $this;
    }

    protected function getTag($tagKey) {

        $tags = [];

        foreach($this->values['tags'] as $tag){
            $tag = explode('=', $tag);
            if(count($tag)>=2){
                $tags[$tag[0]] = $tag[1];
            }
        }

        if(false === empty( $tags[$tagKey] )) {

            return $tags[$tagKey];
        } else {

            return null;
        }
    }

    protected function needsFullObject() {

        if(!$this->isFullObject) {

            $this->getFullObject();

        }
    }

}