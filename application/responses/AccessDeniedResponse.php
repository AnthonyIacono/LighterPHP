<?php

class AccessDeniedResponse extends AppViewResponse {
    public $status = 403;

    public function __construct(AppResource $resource, $properties = array()) {
        parent::__construct($resource, $properties);

        $this->view = 'AccessDeniedPage';

        if($resource->request->is_async) {
            $this->view = 'AccessDeniedOverlay';
            $this->layout = 'ajax';
        }
    }
}