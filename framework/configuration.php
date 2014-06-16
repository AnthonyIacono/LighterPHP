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
 * Class Configuration
 * Using this static class, you are able to read values from your application configuration files.
 */
class Configuration {
    /**
     * Disallow the construction of this class since it is meant to be static.
     */
    private function __construct() {
    }

    /**
     * Maintains a list of configs and values that have been loaded.
     * @var array
     */
    private static $configs = array();

    /**
     * Use this function to load a configuration file before reading values.
     * If you are trying to read values from the configuration file, see the function below.
     * @param $configurationName - The name of the configuration file you want to read values from (without .php)
     * @throws Exception
     */
    public static function load($configurationName) {
        /**
         * We don't need to load configuration files twice.
         */
        if(isset(self::$configs[$configurationName])) {
            return;
        }

        /**
         * Form a path to the desired configuration file.
         * @var string $configurationPath
         */
        $configurationPath = lighter()->get_config_path() . $configurationName . '.php';

        /**
         * Ensure that the configuration file actually exists, or throw an exception.
         */
        if(!file_exists($configurationPath)) {
            throw new Exception("Configuration named \"{$configurationName}\" does not exist.");
        }
        /**
         * Using the path we have formed, include the configuration file just like any other PHP include.
         */
        include_once($configurationPath);

        /**
         * Since variables are brought into the function scope when using include_once()
         * within a method, we can access the $config variable from the file.
         * If it does not exist, just default it to an empty array.
         */
        $config = isset($config) ? $config : array();

        /**
         * Store the configuration we've loaded so we can read the values in Configuration::get()
         */
        self::$configs[$configurationName] = $config;
    }

    /**
     * Use this function to read values from a configuration file that you have loaded.
     * @param $configurationName - The name of the configuration file you want to read values from (without .php)
     * @param $key - The key of the variable you are trying to read from the $config map
     * @return mixed
     * @throws Exception
     */
    public static function get($configurationName, $key) {
        /**
         * First determine that we have loaded the configuration with Configuration::load, otherwise throw an exception.
         */
        if(!isset(self::$configs[$configurationName])) {
            throw new Exception("Configuration named \"{$configurationName}\" has not been loaded.");
        }

        /**
         * Ensure that the variable they are trying to read exists, otherwise throw an exception.
         */
        if(!array_key_exists($key, self::$configs[$configurationName])) {
            throw new Exception("Configuration key \"{$key}\" not found in configuration named \"{$configurationName}\".");
        }

        /**
         * Return the value that has been read from the configuration file.
         */
        return self::$configs[$configurationName][$key];
    }
}