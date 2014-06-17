<?php

class ViewGeneratorShell extends AppShell {
    public function main($arguments) {
        foreach($arguments as $viewName) {
            $this->writeLine("Beginning data collection for generation of: {$viewName}");

            list($baseTableName, $baseTablePrefix) = explode(' ', $this->inputPrompt("What is the base table name and prefix?"), 2);

            $dependencyMap = $this->getDependencyMap($baseTableName, $baseTablePrefix);

            $printableText = $this->printableRawInfo($viewName, $baseTableName, $baseTablePrefix, $dependencyMap);


            $file_path = dirname(__FILE__) . '/output/' . $viewName . '.txt';
            file_put_contents($file_path, $printableText);
            $this->execute("notepad $file_path");
        }
    }

    public function printableRawInfo($viewName, $tableName, $tablePrefix, $dependencyMap) {
        $value = "array(\n";
        $value .= "    'viewName' => '{$viewName}',\n";
        $value .= "    'tableName' => '{$tableName}',\n";
        $value .= "    'tablePrefix' => '{$tablePrefix}',\n";
        $value .= "    'modelType' => '',\n";
        $value .= "    'dependencyMap' => array(\n";

        foreach($dependencyMap as $dependencyMapInfo) {
            $value .= "        array(\n";

            foreach($dependencyMapInfo as $k => $v) {
                $value .= "            '{$k}' => '{$v}',\n";
            }

            $value .= "        ),\n";
        }

        $value .= "    )\n";
        $value .= ")";

        return $value;
    }

    public function buildQuery($baseTableName, $baseTablePrefix, $processedDependencyMap) {
        // Start the SELECT statement
        $query = "SELECT\n\n";

        $tableSchema = $this->database->get_schema_for_table($baseTableName, false, false);

        foreach($tableSchema as $schemaField) {
            $query .= "`{$baseTablePrefix}`.`{$schemaField->Field}` as `{$schemaField->Field}`,\n";
        }

        $query .= "\n";

        foreach($processedDependencyMap as $dependencyInfo) {
            $dependencySchema = $this->database->get_schema_for_table($dependencyInfo['table_name'], false, false);

            foreach($dependencySchema as $dependencySchemaField) {
                $query .= "`{$dependencyInfo['prefix']}`.`{$dependencySchemaField->Field}` as `{$dependencyInfo['prefix']}_{$dependencySchemaField->Field}`,\n";
            }

            $query .= "\n";
        }

        $query = substr($query, 0, strlen($query) - 3);
        $query .= "\n\n";

        // FROM statement
        $query .= "FROM `{$baseTableName}` `$baseTablePrefix`\n";

        // LEFT JOIN statements

        foreach($processedDependencyMap as $dependencyInfo) {
            $query .= "LEFT JOIN `{$dependencyInfo['table_name']}` `{$dependencyInfo['prefix']}` ";
            $query .= "ON `{$dependencyInfo['prefix']}`.`{$dependencyInfo['col1']}` = `{$dependencyInfo['last_prefix']}`.`{$dependencyInfo['col2']}`\n";
        }

        return $query;
    }

    public function getDependencyMap($tableName, $prefixName = '') {
        $prefixPreview = empty($prefixName) ? 'No prefix' : $prefixName;
        $tableSchema = $this->database->get_schema_for_table($tableName, false, false);

        $dependencyMap = array();

        foreach($tableSchema as $schemaField) {
            $this->writeLine("{$tableName} ({$prefixPreview}): {$schemaField->Field}");
        }

        $dependencyInput = $this->inputPrompt("Enter dependency info (seperated by commas):");

        if(empty($dependencyInput)) {
            return array();
        }

        $dependencyPieces = explode(',', $dependencyInput);

        foreach($dependencyPieces as $dependencyPiece) {
            list($table, $prefix, $col1, $col2) = explode(' ', trim($dependencyPiece), 4);

            $dependencyMap[] = array(
                'table_name' => $table,
                'prefix' => $prefix,
                'col1' => $col1,
                'col2' => $col2
            );
        }

        return $dependencyMap;
    }
}