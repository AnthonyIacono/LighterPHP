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
 * Class ViewRenderer
 * Static functions responsible for rendering views throughout your application.
 */
class ViewRenderer {
    /**
     * Disallow the construction of this class since it is meant to be static.
     */
    private function __construct() {
    }

    /**
     * Renders a view using the given variables and returns the output.
     * @param $viewName - Name of view file. Do not include '.php'
     * @param array $viewVariables - Variables to be passed to the view
     * @return string
     * @throws Exception
     */
    public static function render_view($viewName, $viewVariables = array()) {
        $viewPath = lighter()->get_views_path() . $viewName . '.php';

        if(!file_exists($viewPath)) {
            throw new Exception("Unable to find view \"{$viewName}\".");
        }

        return self::extract_and_include($viewVariables, $viewPath);
    }

    /**
     * Magic function used to extract variables to the view and render.
     * @param $__variables
     * @param $__file
     * @return string
     */
    private static function extract_and_include($__variables, $__file) {
        extract($__variables, EXTR_OVERWRITE);

        ob_start();

        include($__file);

        $__contents = ob_get_contents();

        ob_end_clean();

        return $__contents;
    }
}