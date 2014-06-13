<?php
/**
 * =============================================================================
 * LighterPHP
 * Copyright (C) 2014 ASDF LLC.  All rights reserved.
 * =============================================================================
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, version 3.0, as published by the
 * Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Class Route
 * Represents a route to be loaded into the router configuration.
 */
class Route {
    /**
     * Stores the route pattern.
     * @var string
     */
    public $pattern;

    /**
     * Name of resource to route.
     * @var string
     */
    public $resource;

    /**
     * Construct an instance of Route.
     * @param $pattern
     * @param $resource
     */
    public function __construct($pattern, $resource) {
        $this->pattern = $pattern;
        $this->resource = $resource;
    }
}