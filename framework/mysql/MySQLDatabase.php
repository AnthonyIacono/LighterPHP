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
 * Class MySQLDatabase
 * Responsible for connecting to MySQL databases and then performing queries.
 */
class MySQLDatabase {
    /**
     * Stores the instance of mysqli database handle object.
     * @var mysqli|null
     */
    private $database_handle;

    /**
     * Stores the configured MySQL host.
     * @var string
     */
    private $host;

    /**
     * Stores the configured MySQL username.
     * @var string
     */
    private $username;

    /**
     * Stores the configured MySQL password.
     * @var string
     */
    private $password;

    /**
     * Stores the configured MySQL database name.
     * @var string
     */
    private $database_name;

    /**
     * Specifies the port number to attempt to connect to the MySQL server.
     * @var string
     */
    private $port;

    /**
     * Specifies the socket or named pipe that should be used.
     * @var string
     */
    private $socket;

    /**
     * Maintains a list of tables that have already been initiated using the get_table()
     * @var MySQLTable[]
     */
    private $cached_tables = array();

    /**
     * Construct a new instance of MySQLDatabase.
     * This function does not connect to the database, use connect().
     * @param $databaseName
     * @param string|null $host
     * @param string|null $username
     * @param string|null $password
     * @param string|null $port
     * @param string|null $socket
     */
    public function __construct($databaseName, $host = null, $username = null, $password = null, $port = null, $socket = null) {
        $this->database_name = $databaseName;
        $this->host = null === $host ? ini_get("mysqli.default_host") : $host;
        $this->username = null === $username ? ini_get("mysqli.default_user") : $username;
        $this->password = null === $password ? ini_get("mysqli.default_pw") : $password;
        $this->port = null === $port ? ini_get("mysqli.default_port") : $port;
        $this->socket = null === $socket ? ini_get("mysqli.default_socket") : $socket;
    }

    /**
     * Returns the mysqli handle. If there has not been a connection this function returns null.
     * @return mysqli|null
     */
    public function get_database_handle() {
        return $this->database_handle;
    }

    /**
     * Sets the mysqli handle manually. Typically there is no need for this. See the function connect().
     * @param $databaseHandle
     */
    public function set_database_handle($databaseHandle) {
        $this->database_handle = $databaseHandle;
    }

    /**
     * Returns the configured MySQL host.
     * @return string
     */
    public function get_host() {
        return $this->host;
    }

    /**
     * Sets the configured MySQL host. You can using this function before calling connect().
     * @param string $host
     */
    public function set_host($host) {
        $this->host = $host;
    }

    /**
     * Returns the configured MySQL username.
     * @return string
     */
    public function get_username() {
        return $this->username;
    }

    /**
     * Sets the configured MySQL username. You can using this function before calling connect().
     * @param string $username
     */
    public function set_username($username) {
        $this->username = $username;
    }

    /**
     * Returns the configured MySQL password.
     * @return string
     */
    public function get_password() {
        return $this->password;
    }

    /**
     * Sets the configured MySQL password. You can using this function before calling connect().
     * @param string $password
     */
    public function set_password($password) {
        $this->password = $password;
    }

    /**
     * Returns the configured MySQL database name.
     * @return string
     */
    public function get_database_name() {
        return $this->database_name;
    }

    /**
     * Sets the configured MySQL database name. You can using this function before calling connect().
     * @param string $name
     */
    public function set_database_name($name) {
        $this->database_name = $name;
    }

    /**
     * Returns the configured MySQL port.
     * @return string
     */
    public function get_port() {
        return $this->port;
    }

    /**
     * Sets the configured MySQL port. You can using this function before calling connect().
     * @param int|string $port
     */
    public function set_port($port) {
        $this->port = $port;
    }

    /**
     * Returns the configured MySQL socket.
     * @return string
     */
    public function get_socket() {
        return $this->socket;
    }

    /**
     * Sets the configured MySQL socket. You can using this function before calling connect().
     * @param string $socket
     */
    public function set_socket($socket) {
        $this->socket = $socket;
    }

    /**
     * Connect to the MySQL database. See constructor above for more information.
     */
    public function connect() {
        /**
         * Clear the list of cached tables.
         */
        $this->cached_tables = array();

        /**
         * Create our connection to the database using the mysqli class.
         */
        $this->database_handle = new mysqli($this->host, $this->username, $this->password, $this->database_name,
            $this->port, $this->socket);
    }

    /**
     * Retrieve an instance of MySQLTable for a specific table.
     * If called twice for the same table, the result is cached.
     * @param $tableName - Name of table within MySQL database.
     * @return MySQLTable
     */
    public function get_table($tableName) {
        /**
         * Check if we already have a cached table, and if we do then simply return it.
         */
        if(isset($this->cached_tables[$tableName])) {
            return $this->cached_tables[$tableName];
        }

        /**
         * Create an instance of MySQLTable using the database and table name.
         * @var MySQLTable $tableObject
         */
        $tableObject = new MySQLTable($this, $tableName);

        /**
         * Store the table into cache, just in case the function is called again.
         */
        $this->cached_tables[$tableName] = $tableObject;

        /**
         * Finally, return the MySQLTable object.
         */
        return $tableObject;
    }

    /**
     * @var MySQLQueryBenchmark
     */
    public $benchmarker = null;

    public function installBenchmarker(MySQLQueryBenchmark $benchmarker) {
        $this->benchmarker = $benchmarker;
    }

    public function query($query, $result_mode = MYSQLI_STORE_RESULT) {
        if($this->benchmarker !== null) {
            $queryId = $this->benchmarker->beginBenchmarkingQuery($query);
        }

        $result = $this->database_handle->query($query, $result_mode);

        if($this->benchmarker !== null) {
            $this->benchmarker->finishBenchmarkingQuery($queryId);
        }

        if(empty($result)) {
            return 0;
            //die($query . $this->db->errno . ' is last error ' . $this->db->error);
            //throw new Exception("Error with query: {$query}");
        }

        return $result;
    }

    public function tableExists($tableName) {
        $tableNameEncoded = $this->encode($tableName);
        $databaseNameEncoded = $this->encode($this->database_name);

        $results = $this->selectQuery(<<<SQL
SELECT COUNT(*) as `count` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = {$databaseNameEncoded} AND TABLE_NAME = {$tableNameEncoded}
SQL
        );

        $result = $results[0];

        return !empty($result->count);
    }

    public function selectQuery($query, $modelType = null) {
        $result = $this->query($query);

        if(empty($result)) {
            throw new Exception("Query failed: {$query}");
        }

        $results = array();

        while($row = $result->fetch_object()) {
            $results[] = $this->construct_model($row, $modelType);
        }

        return $results;
    }

    public function encode($value) {
        if($value === false) {
            return 'false';
        }

        if($value === true) {
            return 'true';
        }

        return $value === null ? 'null' : "'" . $this->real_escape_string($value) . "'";
    }

    public function construct_model($row, $modelType = null) {
        $maybeModel = ModelCache::$singleton->get_cached_model($row, $modelType);

        if($maybeModel !== null) {
            return $maybeModel;
        }

        $modelType = null === $modelType ? 'MySQLRecord' : $modelType;

        // if the model type is just a class (there is no :: indicating a function call
        if(strpos($modelType, '::') === false) {
            if(method_exists($modelType, 'modelBinder')) {
                $model = $modelType::modelBinder($row, $this);
            }
            else {
                $model = new $modelType();

                foreach($row as $k => $v) {
                    $model->{$k} = $v;
                }
            }
        }
        else {
            $model = call_user_func_array($modelType, array($row, $this));
        }

        ModelCache::$singleton->set_cached_model($row, $modelType, $model);

        return $model;
    }

    /**
     * @return MySQLFieldSchema[]
     */
    public function show_columns($table_name, $try_cache = true, $write_cache = true) {
        $cache_path = Config::$Configs['application']['paths']['cache'] . 'show_columns_' . $this->database_name . '_' . $table_name;

        if($try_cache && file_exists($cache_path)) {
            $schema = json_decode(file_get_contents($cache_path));
            $schema = !is_array($schema) ? array() : $schema;

            return $schema;
        }

        $table_name_escaped = $this->real_escape_string($table_name);

        $result = $this->query("SHOW COLUMNS FROM `{$table_name_escaped}`");

        if(false === $result || !is_object($result)) {
            throw new Exception("Table `{$table_name_escaped}` does not exist");
        }

        $schema = array();

        while($row = $result->fetch_object()) {
            $schema[] = new MySQLFieldSchema($row);
        }

        if($write_cache) {
            if(!is_dir(Config::$Configs['application']['paths']['cache'])) {
                @mkdir(Config::$Configs['application']['paths']['cache'], 0777, true);
            }
            file_put_contents($cache_path, json_encode($schema));
        }

        return $schema;
    }
}