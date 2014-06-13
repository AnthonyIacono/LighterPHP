<?php

class HomeResource extends AppResource {
    public function execute() {
        return new ViewResponse('HomePage', array(
            'x' => 4
        ));
    }
}