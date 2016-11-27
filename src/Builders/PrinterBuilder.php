<?php
namespace Lightmedia\Googleprint\Builders;

use Lightmedia\Googleprint\Exceptions\GooglePrintException;
use Lightmedia\Googleprint\Models\Printer;

/**
 * Class Search
 *
 * @package App\GooglePrint\Resources
 */
class PrinterBuilder {

    protected $accessToken;

    protected $values = [
        'extra_fields' => 'connectionStatus,semanticState,uiState',
    ];

    protected $printerTypes = [
        'GOOGLE',
        'HP',
        'DOCS',
        'DRIVE',
        'FEDEX',
        'ANDROID_CHROME_SNAPSHOT',
        'IOS_CHROME_SNAPSHOT',
    ];

    protected $printerStatuses = [
        'ALL',
        'ONLINE',
        'UNKNOWN',
        'OFFLINE',
        'DORMANT',
    ];

    public function searchFor($searchText) {

        $this->values['q'] = $searchText;

        return $this;
    }

    public function recent() {

        $this->values['q'] = '^recent';

        return $this;
    }

    public function own() {

        $this->values['q'] = '^own';

        return $this;
    }

    public function shared() {

        $this->values['q'] = '^shared';

        return $this;
    }

    public function type($type) {

        $type = strtoupper($type);

        if(false === in_array($type, $this->printerTypes)) {
            throw new GooglePrintException('Printer type ' . $type . ' does not exist');
        }

        $this->values['type'] = $type;

        return $this;
    }

    public function hasStatus($status) {

        $status = strtoupper($status);

        if(false === in_array($status, $this->printerStatuses)) {
            throw new GooglePrintException('Printer status ' . $status . ' does not exist');
        }

        $this->values['status'] = $status;

        return $this;
    }

    public function isOnline() {

        $this->values['status'] = 'ONLINE';

        return $this;
    }

    public function isOffline() {

        $this->values['status'] = 'OFFLINE';

        return $this;
    }

    public function isDormant() {

        $this->values['status'] = 'DORMANT';

        return $this;
    }

    public function all() {

        $this->values['connection_status'] = 'ALL';

        return $this;
    }

    public function get() {

        $search   = new ConnectionBuilder($this->accessToken);
        $response = $search->search($this->values);


        return $this->buildObjects($response['printers']);

    }

    protected function arrayToHash(array $array) {

        return md5(serialize($array));
    }

    protected function buildObjects(Array $array) {

        $return = [ ];

        $hide = config('print.printers.hide') ?: [ ];

        foreach($array as $item) {

            if(false === in_array($item['id'], $hide)) {

                $object   = new Printer($item['id']);
                $return[] = $object->assign($item);
            }

        }

        return collect($return);
    }

    public function find($id) {

        $params = [
            'printerid'    => $id,
            'extra_fields' => 'connectionStatus,semanticState,uiState',
        ];

        $search   = new ConnectionBuilder;
        $response = $search->printer($params);

        if(count($response['printers']) !== 1){
            throw new GooglePrintException('Unable to find printer');
        }

        $object = new Printer;
        $object->assign($response['printers'][0]);

        return $object;
    }
}
