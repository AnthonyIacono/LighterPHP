<?php

class AppShell {
    /**
     * @var MySQLDatabase
     */
    public $database;

    public $outputDir;

    public function __construct() {
        $this->outputDir = dirname(__FILE__) . '/output/';

        $this->database = MySQLPool::$singleton->database(Config::$Configs['mysql']);
    }

    public function main($arguments) {
        throw new Exception("Not implemented: " . get_class($this) . '::main()');
    }

    public function writeLine($str) {
        echo($str . "\n");
    }

    public function inputPrompt($message) {
        fwrite(STDOUT, $message . ' ');
        return preg_replace('/\\r\\n$/', '', fgets(STDIN));
    }

    public function execute($command) {
        $output = array();

        exec($command, $output, $returnValue);

        implode("\n", $output);

        return array(
            'text' => implode("\n", $output),
            'return' => $returnValue
        );
    }

    public function execShell($command) {
        $output = array();

        exec($command, $output, $returnValue);

        return implode("\n", $output);
    }

    public function get_all_files_in_directory($directory) {
        $handle = opendir($directory);
        $files = array();

        while (false !== ($file = readdir($handle))) {
            if(in_array($file, array('.', '..'))) {
                continue;
            }

            $files[] = $file;
        }

        closedir($handle);
        return $files;
    }
}
