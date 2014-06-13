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

class HTMLHelper {

    public function js($file, $inLine = false) {
        Config::Import('application');

        $version = strstr($file, '?')
            ? '&v=' . Config::$Configs['application']['version']
            : '?v=' . Config::$Configs['application']['version'];

        $output = "<script type=\"text/javascript\" src=\"$file$version\"></script>";

        if (!$inLine) $output .= "\n";

        return $output;
    }

    public function css($file, $media = null) {
        Config::Import('application');

        $version = strstr($file, '?')
            ? '&v=' . Config::$Configs['application']['version']
            : '?v=' . Config::$Configs['application']['version'];

        $css = "<link rel=\"stylesheet\" type=\"text/css\" href=\"$file$version\"";

        if ($media !== null)
            $css .= " media=\"{$media}\"";

        $css .= " />\n";

        return $css;
    }
}