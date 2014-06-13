<?php

class SmartRedirectResponse extends Response {
    public $status = 302;

    /**
     * @var AppResource
     */
    public $resource;

    public $uri;

    public function __construct(AppResource $resource, $uri, $properties = array()) {
        parent::__construct($resource, $properties);

        $this->resource = $resource;
        $this->uri = $uri;

        if($this->resource->request->is_async) {
            $this->status = 200;
            return;
        }

        $this->headers['Location'] = $this->uri;
    }

    public function pre_render() {
        $this->headers['X-Query-Time'] = $this->resource->database->benchmarker->getTotalTime();
        $this->headers['X-Query-Count'] = count($this->resource->database->benchmarker->queries);

        parent::pre_render();
    }
    public function render() {
        if($this->resource->request->is_async) {
            $uri_js = json_encode($this->uri);
            echo <<<HTML
<script type="text/javascript">
document.location = {$uri_js};
</script>
HTML;

            return;
        }

        parent::render();
    }
}