<?php

class SchemaExportShell extends  AppShell {
    public function main($arguments) {
        $output = '';

        $tableRecords = \Plinq\Plinq::factory($this->database->selectQuery("SHOW TABLE STATUS FROM `{$this->database->database}`"))->Where(function($_, $record) use($arguments) {
            return preg_match('/\\_view$/', $record->Name) || (!empty($arguments) && !in_array($record->Name, $arguments)) ? false : true;
        })->ToArray();

        $tables = array();

        foreach($tableRecords as $tableRecord) {
            $records = $this->database->selectQuery("SHOW CREATE TABLE `{$this->database->database}`.`{$tableRecord->Name}`");
            $record = $records[0];

            $create_table_column = 'Create Table';

            $table_keys = array();
            $table_columns = array();

            preg_replace_callback('/([A-Z\\ ]+?)KEY\\ (\\`[a-zA-Z0-9\\_]+\\`)?([^\\)]+)\\)\\,?/', function($match) use(&$table_keys) {
                $type = trim($match[1]);
                $name = preg_replace('/^\\`/', '', trim($match[2]));
                $name = preg_replace('/\\`$/', '', $name);

                $columns = preg_replace('/^\\(/', '', trim($match[3]));

                $columns_parts = \Plinq\Plinq::factory(explode(',', $columns))->Select(function($_, $column_name) {
                    $column_name = preg_replace('/^\\`/', '', $column_name);
                    $column_name = preg_replace('/\\`$/', '', $column_name);
                    return $column_name;
                })->ToArray();

                $table_keys[] = array(
                    'has_type' => empty($type) ? false : true,
                    'type' => $type,
                    'has_name' => empty($name) ? false : true,
                    'name' => $name,
                    'columns' => $columns_parts

                );
            }, $record->{$create_table_column});

            $columnStats = $this->database->show_columns($tableRecord->Name, false, false);

            foreach($columnStats as $columnSchema) {
                $table_columns[$columnSchema->Field] = array(
                    'auto_incrementing' => strtolower($columnSchema->Extra) == 'auto_increment' ? true : false,
                    'type' => $columnSchema->Type,
                    'allow_null' => empty($columnSchema->Null) ? false : true,
                    'has_default' => $columnSchema->HasDefault,
                    'default' => $columnSchema->Default
                );
            }

            $tableData = array(
                'columns' => $table_columns,
                'keys' => $table_keys
            );
            $tables[] = $tableData;

            $output .= $this->printableRawInfo($tableRecord->Name, $tableData) . "\n";
        }

        file_put_contents('ses.txt', $output);
    }

    public function printableRawInfo($tableName, $table) {
        $value = "'{$tableName}' => array(\n";
        $value .= "    'columns' => array(\n";

        foreach($table['columns'] as $key => $dependencyMapInfo) {
            $value .= "        '{$key}' => array(\n";

            foreach($dependencyMapInfo as $k => $v) {
                if(is_string($v)) {
                    $v = "'{$v}'";
                }
                else if($v === true) {
                    $v = "true";
                }
                else if($v === false) {
                    $v = "false";
                }
                else if($v === null) {
                    $v = "null";
                }
                else if(is_numeric($v)) {
                    $v = "'{$v}'";
                }
                else if(is_array($v)) {
                    $v = 'array(' . implode(', ', \Plinq\Plinq::factory($v)->Select(function($_, $text) {
                        return "'{$text}'";
                    })->ToArray()) . ')';
                }

                $value .= "            '{$k}' => {$v},\n";
            }

            $value .= "        ),\n";
        }

        $value .= "    ),\n";

        $value .= "    'keys' => array(\n";

        foreach($table['keys'] as $dependencyMapInfo) {
            $value .= "        array(\n";

            foreach($dependencyMapInfo as $k => $v) {
                if(is_string($v)) {
                    $v = "'{$v}'";
                }
                else if($v === true) {
                    $v = "true";
                }
                else if($v === false) {
                    $v = "false";
                }
                else if($v === null) {
                    $v = "null";
                }
                else if(is_numeric($v)) {
                    $v = "'{$v}'";
                }
                else if(is_array($v)) {
                    $v = 'array(' . implode(', ', \Plinq\Plinq::factory($v)->Select(function($_, $text) {
                        return "'{$text}'";
                    })->ToArray()) . ')';
                }

                $value .= "            '{$k}' => {$v},\n";
            }

            $value .= "        ),\n";
        }

        $value .= "    ),\n";

        $value .= "),";

        return $value;
    }
}