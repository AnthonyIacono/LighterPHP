<?php

class WelcomeResource extends Resource {
    public function execute() {
        $routeParams = lighter()->get_route_params();

        return new ViewResponse('WelcomePage', array(
            'name' => $routeParams['named']['name']
        ));
    }
}