<?php

class RawViewGeneratorShell extends ViewGeneratorShell {
    public $rawInfo = array(

    );

    public function processDependencyMap($baseTableName, $baseTablePrefix, $dependencyMap, $isReallyPrefixed = false) {
        $processedDependencyMap = array();

        foreach($dependencyMap as $unprocessedDependencyInfo) {
            $processedPrefix = $isReallyPrefixed ? ($baseTablePrefix . '_' . $unprocessedDependencyInfo['prefix']) : $unprocessedDependencyInfo['prefix'];
            $processedTableName = $unprocessedDependencyInfo['table_name'];

            $processedDependencyMap[] = array(
                'table_name' => $processedTableName,
                'prefix' => $processedPrefix,
                'col1' => $unprocessedDependencyInfo['col1'],
                'col2' => $unprocessedDependencyInfo['col2'],
                'last_prefix' => $baseTablePrefix,
                'last_table' => $baseTableName
            );

            $rawDependencyInfo = \Plinq\Plinq::factory($this->rawInfo)->Single(function($k, $v) use ($processedTableName) {
                return $v['tableName'] == $processedTableName;
            });

            $rawDependencyMap = $rawDependencyInfo['dependencyMap'];

            if(empty($rawDependencyMap)) {
                continue;
            }

            $processedSubDependencyMap = $this->processDependencyMap($unprocessedDependencyInfo['table_name'], $processedPrefix, $rawDependencyMap, true);

            $processedDependencyMap = array_merge($processedDependencyMap, $processedSubDependencyMap);
        }

        return $processedDependencyMap;
    }

    public function main($arguments) {
        $outputDir = dirname(__FILE__) . '/output/';
        if(!file_exists($outputDir)) {
            @mkdir($outputDir, 0777, true);
        }

        $success = 0;
        $attempts = 0;

        // Remove all cached files
        $cacheFiles = $this->get_all_files_in_directory(Config::$Configs['application']['paths']['cache']);

        foreach($cacheFiles as $cacheFile) {
            @unlink(Config::$Configs['application']['paths']['cache'] . $cacheFile);
        }

        foreach($this->rawInfo as $rawInfo) {
            if(!empty($arguments) && !in_array($rawInfo['viewName'], $arguments)) {
                continue;
            }

            // delete this view for now
            $tableExists = $this->database->tableExists($rawInfo['viewName']);

            if($tableExists) {
                $this->database->query("DROP VIEW `{$rawInfo['viewName']}`");
            }

            // make it fake style
            $this->database->query("CREATE ALGORITHM = UNDEFINED VIEW `{$rawInfo['viewName']}` AS SELECT 0 as `id`");
        }

        foreach($this->rawInfo as $rawInfo) {
            if(!empty($arguments) && !in_array($rawInfo['viewName'], $arguments)) {
                continue;
            }

            $attempts++;

            $outputPath = $outputDir . $rawInfo['viewName'] . '.sql';

            $tableExists = $this->database->tableExists($rawInfo['viewName']);

            if($tableExists) {
                $this->database->query("DROP VIEW `{$rawInfo['viewName']}`");
            }

            $processedDependencyMap = $this->processDependencyMap($rawInfo['tableName'], $rawInfo['tablePrefix'], $rawInfo['dependencyMap'], false);

            $query = $this->buildQuery($rawInfo['tableName'], $rawInfo['tablePrefix'], $processedDependencyMap);
            file_put_contents($outputPath, $query);
            $this->database->query("CREATE VIEW `{$rawInfo['viewName']}` AS \n" . $query);

            $result = $this->execute("php ShellRunner.php ModelBindingCheckerShell \"{$rawInfo['viewName']} {$rawInfo['modelType']}\"");

            if(trim($result['text']) != '') {
                $this->writeLine($result['text']);
                continue;
            }

            $success++;
            //$this->writeLine("\t- View created: " . $rawInfo['viewName'] . ' and verified as model: ' . $rawInfo['modelType']);
        }

        // Remove all cached files
        $cacheFiles = $this->get_all_files_in_directory(Config::$Configs['application']['paths']['cache']);

        foreach($cacheFiles as $cacheFile) {
            @unlink(Config::$Configs['application']['paths']['cache'] . $cacheFile);
        }

        $this->writeLine("Succeeded in {$success} / {$attempts} attempts");
    }
}