<?php

class AsyncifyResponse extends Response {
    public $status = 302;

    /**
     * @var AppResource
     */
    public $resource;

    public function __construct(AppResource $resource, $baseUri) {
        parent::__construct(array(
            'headers' => array(
                'Location' => $baseUri . '#!' . $resource->request->uri
            )
        ));
    }
}