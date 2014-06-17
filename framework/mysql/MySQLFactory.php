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
 * Class MySQLFactory
 * Responsible for creating and caching MySQL database objects.
 */
class MySQLFactory {
    /**
     * Stores a list of database objects cached by the factory.
     * @var MySQLDatabase[]
     */
    private $cached_databases = array();

    /**
     * Creates and returns new instance of MySQLDatabase or returns instance already cached by the factory.
     * This function DOES NOT call connect() on the database object automatically.
     * @param $databaseName - Name of MySQL database.
     * @param null $host - Host of MySQL server.
     * @param null $username - Username for MySQL authentication.
     * @param null $password - Password for MySQL authentication.
     * @param null $port - Port of MySQL server.
     * @param null $socket - Socket to be used for MySQL connection.
     * @return MySQLDatabase
     */
    public function get_mysql_database($databaseName, $host = null, $username = null, $password = null, $port = null, $socket = null) {
        /**
         * By default, use variables defined in php.ini
         */
        $host = null === $host ? ini_get("mysqli.default_host") : $host;
        $username = null === $username ? ini_get("mysqli.default_user") : $username;
        $password = null === $password ? ini_get("mysqli.default_pw") : $password;
        $port = null === $port ? ini_get("mysqli.default_port") : $port;
        $socket = null === $socket ? ini_get("mysqli.default_socket") : $socket;

        /**
         * Loop through the cached databases and compare the parameters to determine if we have a match.
         * @var MySQLDatabase $cachedDatabase
         */
        foreach($this->cached_databases as $cachedDatabase) {
            /**
             * Every parameter for the database must match, otherwise we continue the loop.
             */
            if($cachedDatabase->get_host() != $host
                or $cachedDatabase->get_username() != $username
                or $cachedDatabase->get_password() != $password
                or $cachedDatabase->get_database_name() != $databaseName
                or $cachedDatabase->get_port() != $port
                or $cachedDatabase->get_socket() != $socket) {
                continue;
            }

            /**
             * Since all the parameters match, we return the cached database object.
             */
            return $cachedDatabase;
        }

        /**
         * Because there was not a database object cached, we need to construct one and cache it.
         */
        $mysqlDatabase = new MySQLDatabase($databaseName, $host, $username, $password, $port, $socket);
        $this->cached_databases[] = $mysqlDatabase;

        /**
         * Return the newly created database object.
         */
        return $mysqlDatabase;
    }

    /**
     * Wrapper for get_mysql_database() using MySQL application configuration file.
     */
    public function get_default_mysql_database() {
        /**
         * Load the MySQL configuration.
         */
        Configuration::load('MySQL');

        /**
         * Retrieve values specified in the configuration loaded above.
         */
        $host = Configuration::get('MySQL', 'host');
        $databaseName = Configuration::get('MySQL', 'database_name');
        $socket = Configuration::get('MySQL', 'socket');
        $username = Configuration::get('MySQL', 'username');
        $password = Configuration::get('MySQL', 'password');
        $port = Configuration::get('MySQL', 'port');

        /**
         * Call get_mysql_database() with the values loaded from the configuration and return the result.
         */
        return $this->get_mysql_database($databaseName, $host, $username, $password, $port, $socket);
    }
}