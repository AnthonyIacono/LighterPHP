<?php

class ErrorResponse extends AppViewResponse {
    public $status = 200;

    public function __construct(AppResource $resource, $errors = array(), $properties = array()) {
        parent::__construct($resource, $properties);

        if($resource->request->is_async) {
            $this->view = 'ErrorOverlay';
            $this->layout = '';
        }
        else {
            $this->view = 'ErrorPage';
        }

        $this->variables['errors'] = $errors;
    }
}