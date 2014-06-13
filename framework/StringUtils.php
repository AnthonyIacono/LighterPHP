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
 * Class StringUtils
 * Using this static class you are able to perform common tasks related to string mutation.
 */
class StringUtils {
    /**
     * Disallow the construction of this class since it is meant to be static.
     */
    private function __construct() {
    }

    /**
     * Returns true if the string provided is blank or contains only whitespace.
     * @param $str
     * @return bool
     */
    public static function is_empty($str) {
        return trim($str) == '';
    }

    /**
     * Generate a MySQL compliant Date from a UNIX timestamp.
     * @param $timestamp
     * @return string
     */
    public static function mysql_date($timestamp = null) {
        /**
         * Check if $timestamp is null, which means they want the Date of the current time.
         */
        if($timestamp === null) {
            $timestamp = time();
        }

        return date('Y-m-d', $timestamp);
    }

    /**
     * Generate a MySQL compliant DateTime from a UNIX timestamp.
     * @param $timestamp
     * @return string
     */
    public static function mysql_datetime($timestamp = null) {
        /**
         * Check if $timestamp is null, which means they want the DateTime of the current time.
         */
        if($timestamp === null) {
            $timestamp = time();
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * Returns true if the string provided is a GUID with the following format: XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX
     * If the GUID provided contains braces or does not contain dashes, this function will return false.
     * @param $str
     * @return bool
     */
    public static function is_guid($str) {
        return preg_match("/^[A-Fa-f0-9]{8}-([A-Fa-f0-9]{4}-){3}[A-Fa-f0-9]{12}$/", $str) ? true : false;
    }

    /**
     * Generate a random GUID in the following format: XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX
     * @param bool $includeDashes - Pass in false if you do not want dashes in the generated GUID.
     * @return string
     */
    public static function guid($includeDashes = true) {
        $tl = str_pad(dechex(mt_rand(0, 65535)), 4, '0', STR_PAD_LEFT) . str_pad(dechex(mt_rand(0, 65535)), 4, '0', STR_PAD_LEFT);
        $tm = str_pad(dechex(mt_rand(0, 65535)), 4, '0', STR_PAD_LEFT);
        $th = mt_rand(0, 255);
        $th = $th & hexdec('0f');
        $th = $th ^ hexdec('40');
        $th = str_pad(dechex($th), 2, '0', STR_PAD_LEFT);
        $cs = mt_rand(0, 255);
        $cs = $cs & hexdec('3f');
        $cs = $cs ^ hexdec('80');
        $cs = str_pad(dechex($cs), 2, '0', STR_PAD_LEFT);
        $clock_seq_low = str_pad(dechex(mt_rand(0, 65535)), 4, '0', STR_PAD_LEFT);
        $node = str_pad(dechex(mt_rand(0, 65535)), 4, '0', STR_PAD_LEFT) . str_pad(dechex(mt_rand(0, 65535)), 4, '0', STR_PAD_LEFT)
            . str_pad(dechex(mt_rand(0, 65535)), 4, '0', STR_PAD_LEFT);

        $newGuid = $tl . '-' . $tm . '-' . $th . $cs . '-' . $clock_seq_low . '-' . $node;

        if(!$includeDashes) {
            $newGuid = str_replace('-', '', $newGuid);
        }

        return $newGuid;
    }

    /**
     * Truncate a string to a specific maximum length. This function removes trims whitespace before appending the suffix.
     * If the provided string does not exceed the maximum length, the exact same string will be returned.
     * @param string $str
     * @param $maxLength
     * @param string $suffix
     * @return string
     */
    public static function truncate($str, $maxLength, $suffix = '...') {
        if(strlen($str) > $maxLength) {
            return trim(substr($str, 0, $maxLength - strlen($suffix))) . $suffix;
        }

        return $str;
    }
}