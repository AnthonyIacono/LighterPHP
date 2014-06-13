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
 * Class Globals
 * Using this static class you are able to access common global variables referenced throughout
 * the lifetime of an HTTP request handled by LighterPHP. Below the class you will find several
 * helper functions.
 */
class Globals {
    /**
     * Disallow the construction of this class since it is meant to be static.
     */
    private function __construct() {
    }

    /**
     * Contains the Lighter framework object used throughout the application. (aka global framework object)
     * @var Lighter|null $lighter
     */
    static public $lighter = null;
}