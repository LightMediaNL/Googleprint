<?php
namespace Lightmedianl\Googleprint\Models;

use View;
use Lightmedianl\Googleprint\Builders\QueryBuilder;
use Lightmedianl\Googleprint\Exceptions\GooglePrintException;

class PrintJob {

    protected $id;
    protected $title;
    protected $printer;
    protected $content;
    protected $new = true;
    protected $contentType;
    protected $fillable = [ ];
    protected $contentTypes = ['url', 'application/pdf', 'image/jpeg', 'image/png', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'text/html', 'text/plain', 'application/postscript' ];

    protected function getSetMethodName($name) {

        return 'set' . ucfirst(camel_case($name)) . 'Attribute';
    }

    public function save() {

        $params = [
            'title'     => $this->getTitle(),
            'ticket'    => $this->getTicket(),
            'content'   => $this->getContent(),
            'printerid' => $this->getPrinterId(),
            'contentType' => $this->getContentType(),
        ];

        $request = new QueryBuilder();
        $response = $request->submit($params);

        $this->assign($response['job']);

        return true;
    }

    public function assign(array $array) {

        $this->values = $array;
        $this->id = $array['id'];
        $this->new = false;

        return $this;
    }

    protected function getPrinterId() {

        if(false === empty( $this->printer )) {
            return $this->printer->id;
        } else if(false === empty( config('print.printer.default') )) {
            return config('print.printer.default');
        } else {
            throw new GooglePrintException('No printer found for print job');
        }
    }

    protected function getContentType() {

        if(false === empty($this->contentType)){
            return $this->contentType;
        }else{
            return null;
        }

    }

    protected function getTitle() {

        if(null !== $this->title) {
            return $this->title;
        } else if(false === empty( config('print.jobs.defaultTitle') )) {
            return config('print.jobs.defaultTitle');
        } else {
            throw new GooglePrintException('Unable to find new job title');
        }
    }

    protected function getContent() {

        if(true === empty( $this->content )) {
            throw new GooglePrintException('No content found to print');
        }

        return $this->content;
    }

    protected function getTicket() {

        $ticket = (object)[
            'version' => '1.0',
            'print'   => (object)[
                'vendor_ticket_item' => [ ],
            ],
        ];

        return json_encode($ticket);
    }

    public function printer(Printer &$printer) {

        $this->printer = &$printer;
    }

    public function view($view, $variables = [ ]) {

        $view          = View::make($view, $variables);
        $this->content = $view->render();
        $this->contentType = 'text/html';

        return $this;
    }

    public function url($url){

        $this->content = $url;
        $this->contentType = 'url';

        return $this;
    }

}