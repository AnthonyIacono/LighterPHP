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
 * Using this function, you can access the global framework object.
 * @return Lighter
 */
function lighter() {
    if(Globals::$lighter === null) {
        Globals::$lighter = new Lighter();
    }

    return Globals::$lighter;
}

/**
 * Using this function, you can set the global framework object.
 * @param Lighter $framework
 */
function install_lighter(Lighter $framework) {
    Globals::$lighter = $framework;
}

/**
 * Using this function, you can include() a PHP file without extracting variables to the local context.
 * You should always include files this way unless you know what you are doing.
 * @param string $filePath
 */
function lighter_include($filePath) {
    include($filePath);
}

/**
 * Using this function, you can include_once() a PHP file without extracting variables to the local context.
 * You should always include_once() files this way unless you know what you are doing.
 * @param string $filePath
 */
function lighter_include_once($filePath) {
    include_once($filePath);
}

/**
 * Using this function, you can require() a PHP file without extracting variables to the local context.
 * You should always require() files this way unless you know what you are doing.
 * @param string $filePath
 */
function lighter_require($filePath) {
    require($filePath);
}

/**
 * Using this function, you can require_once() a PHP file without extracting variables to the local context.
 * You should always require_once() files this way unless you know what you are doing.
 * @param string $filePath
 */
function lighter_require_once($filePath) {
    require_once($filePath);
}