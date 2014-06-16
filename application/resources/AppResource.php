<?php

class AppResource extends Resource {
    /**
     * @var MySQLDatabase
     */
    private $database;

    public function pre_execute() {
        $host = Configuration::get('MySQL', 'host');
        $databaseName = Configuration::get('MySQL', 'database_name');
        $socket = Configuration::get('MySQL', 'socket');
        $username = Configuration::get('MySQL', 'username');
        $password = Configuration::get('MySQL', 'password');
        $port = Configuration::get('MySQL', 'port');

        $this->database = lighter()->get_mysql_factory()
            ->get_mysql_database($databaseName, $host, $username, $password, $port, $socket);

        $this->database->connect();
    }
}