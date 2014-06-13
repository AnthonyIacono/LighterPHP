<?php

class JsonResponse extends Response {

    public function __construct(AppResource $resource, $jsonData, $statusCode = 200) {
        parent::__construct(array(
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($jsonData),
            'status' => $statusCode
        ));
    }
}