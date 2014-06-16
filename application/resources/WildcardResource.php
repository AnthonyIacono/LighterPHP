<?php

class WildcardResource extends AppResource {
    public function execute() {
        print_r(lighter()->get_route());
        print_r(lighter()->get_route_params());

        die("");
    }
}