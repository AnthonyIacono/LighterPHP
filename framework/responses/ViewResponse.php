<?php

class ViewResponse extends Response {
    public $view = '';
    public $layout = 'Default';
    public $variables = array();

    public function __construct($view, $variables = array(), $layout = 'Default') {
        $this->view = $view;
        $this->variables = $variables;
        $this->layout = $layout;
    }

    public function render_body() {
        $viewContents = ViewRenderer::render_view($this->view, $this->variables);

        if(empty($this->layout)) {
            return $viewContents;
        }

        if(is_array($this->layout)) {
            foreach($this->layout as $layout) {
                $viewContents = ViewRenderer::render_view("layouts/{$layout}", array_merge($this->variables, array(
                    'content_for_layout' => $viewContents
                )));
            }

            return $viewContents;
        }

        return ViewRenderer::render_view("layouts/{$this->layout}", array_merge($this->variables, array(
            'content_for_layout' => $viewContents
        )));
    }
}