<?php

class TableCreationShell extends  AppShell {
    public function main($arguments) {
        $tables = array(

        );

        foreach($tables as $table_name => $table_info) {
            $query = <<<SQL
CREATE TABLE IF NOT EXISTS `{$table_name}` (
SQL;

            foreach($table_info['columns'] as $column_name => $column_info) {
                $query .= "\n";
                $query .= "`{$column_name}` {$column_info['type']} ";

                // if it is allow null but there is no default
                if($column_info['allow_null'] && !$column_info['has_default']) {
                    $query .= "DEFAULT NULL";
                }
                else if($column_info['allow_null'] && $column_info['has_default']) {
                    $query .= "DEFAULT " . $this->database->encode_value($column_info['default']);
                }
                else if(!$column_info['allow_null'] && !$column_info['has_default']) {
                    $query .= "NOT NULL";
                }
                else {
                    $query .= "DEFAULT " . $this->database->encode_value($column_info['default']);
                }

                if($column_info['auto_incrementing']) {
                    $query .= " AUTO_INCREMENT ";
                }

                $query .= ",";
            }

            foreach($table_info['keys'] as $key_info) {
                $query .= "\n";

                if($key_info['has_type']) {
                    $query .= $key_info['type'] . ' ';
                }

                $query .= 'KEY ';

                if($key_info['has_name']) {
                    $query .= "`{$key_info['name']}` ";
                }

                $str = implode(', ', \Plinq\Plinq::factory($key_info['columns'])->Select(function($_, $column_name) {
                    return "`{$column_name}`";
                })->ToArray());

                $query .= "({$str}),";
            }

            $query = preg_replace('/\\,$/', '', $query);

            $query .= "\n) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

            $this->database->query($query);
        }
    }
}