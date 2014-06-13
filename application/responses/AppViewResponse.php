<?php

class AppViewResponse extends ViewResponse {
    /**
     * @var AppResource
     */
    public $resource;

    public $layout = array('default');

    public function __construct(AppResource $resource, $properties = array()) {
        parent::__construct($properties);

        if(preg_match('/Overlay$/', $this->view) || preg_match('/Transition$/', $this->view) || preg_match('/Tooltip$/', $this->view)) {
            $this->layout = 'ajax';
        }

        $this->resource = $resource;
    }

    public function pre_render() {
        $this->headers['X-Query-Time'] = $this->resource->database->benchmarker->getTotalTime();
        $this->headers['X-Query-Count'] = count($this->resource->database->benchmarker->queries);

        parent::pre_render();
    }
    public function render() {
        parent::render();
    }
}