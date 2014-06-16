<?php

class MySQLFactory {
    /**
     * @var MySQLDatabase[]
     */
    public $cached_databases = array();

    public function get_mysql_database($databaseName, $host = null, $username = null, $password = null, $port = null, $socket = null) {
        $host = null === $host ? ini_get("mysqli.default_host") : $host;
        $username = null === $username ? ini_get("mysqli.default_user") : $username;
        $password = null === $password ? ini_get("mysqli.default_pw") : $password;
        $port = null === $port ? ini_get("mysqli.default_port") : $port;
        $socket = null === $socket ? ini_get("mysqli.default_socket") : $socket;

        /**
         * @var MySQLDatabase $cachedDatabase
         */
        foreach($this->cached_databases as $cachedDatabase) {
            if($cachedDatabase->get_host() != $host
                or $cachedDatabase->get_username() != $username
                or $cachedDatabase->get_password() != $password
                or $cachedDatabase->get_database_name() != $databaseName
                or $cachedDatabase->get_port() != $port
                or $cachedDatabase->get_socket() != $socket) {
                continue;
            }

            return $cachedDatabase;
        }

        $mysqlDatabase = new MySQLDatabase($databaseName, $host, $username, $password, $port, $socket);
        $this->cached_databases[] = $mysqlDatabase;

        return $mysqlDatabase;
    }
}