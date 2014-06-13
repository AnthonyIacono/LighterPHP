<?php

class NotFoundResponse extends ViewResponse {
    public $status = 404;

    public function __construct() {
        $this->view = 'NotFoundPage';

        if(lighter()->get_request()->is_async) {
            $this->layout = 'ajax';
        }
    }
}