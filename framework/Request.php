<?php

class Request {
    public $verb;
    public $uri;
    public $query_string;

    public $data = array();
    public $files = array();

    public $is_get;
    public $is_post;
    public $is_delete;
    public $is_put;

    /**
     * @var bool
     */
    public $is_async = null;

    public function __construct($verb, $requestUri, $queryString, $data, $files, $isAsync) {
        $this->verb = $verb;
        $this->uri = $requestUri;
        $this->query_string = $queryString;
        $this->data = $data;
        $this->files = $files;
        $this->is_async = $isAsync;

        $verb_lower = strtolower($this->verb);

        $this->is_get = $verb_lower == 'get';
        $this->is_post = $verb_lower == 'post';
        $this->is_delete = $verb_lower == 'delete';
        $this->is_put = $verb_lower == 'put';
    }

    public function getUri($include_query_string = false) {
        if($include_query_string) {
            return $this->uri;
        }

        $pieces = explode('?', $this->uri);

        return $pieces[0];
    }

    public function getData($key) {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function getFile($key) {
        return isset($this->files[$key]) ? $this->files[$key] : null;
    }
}