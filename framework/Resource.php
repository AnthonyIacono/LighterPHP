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
 * Class Resource
 * Each incoming request is routed to a resource, which is constructed by the framework.
 * The resource is responsible for executing business logic and returning a response object.
 */
class Resource {
    /**
     * Called before a request is executed as an opportunity to supercede the execute() function.
     * If you return a response object to this function, execute() will no longer be called.
     * If you return anything else to this function, execute() will be called normally.
     * @return null|Response
     */
    public function pre_execute() {
        return null;
    }

    /**
     * Called after pre_execute(), unless a response object was returned.
     * Here you should execute your business logic and return a response object.
     * @throws Exception
     * @return Response
     */
    public function execute() {
        throw new Exception("Resource named \"" . get_class() . "\" has not implemented execute()");
    }
}