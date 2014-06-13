<?php

class Response {
    public $status = 200;
    public $headers = array();
    public $strictHeaders = array();
    public $body;

    public function before_render() {
        header('HTTP/1.1 ' . HTTPUtils::get_status($this->status));

        if(is_array($this->headers)) {
            foreach($this->headers as $k => $v) {
                header("{$k}: {$v}");
            }
        }
        else if(is_string($this->headers)) {
            header($this->headers);
        }

        if(is_array($this->strictHeaders)) {
            foreach($this->strictHeaders as $k => $v) {
                header("{$k}: {$v}", true);
            }
        }
        else if(is_string($this->strictHeaders)) {
            header($this->strictHeaders, true);
        }
    }

    public function render_body() {
        return "{$this->body}";
    }
}