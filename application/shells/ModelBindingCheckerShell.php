<?php

class ModelBindingCheckerShell extends  AppShell {
    public function main($arguments) {
        $incInt = 1;

        foreach($arguments as $arg) {
            $argParts = explode(' ', $arg);
            $table_name = $argParts[0];
            $model_name = $argParts[1];

            $schema = $this->database->show_columns($table_name, false, false);

            $fakeData = array();

            if(!class_exists($model_name)) {
                $this->writeLine("Class does not exist: {$model_name}");
                continue;
            }

            $classVars = array_keys(get_class_vars($model_name));

            foreach($schema as $fieldSchema) {
                /**
                 * @var MySQLFieldSchema $fieldSchema
                 */
                $fakeData[$fieldSchema->Field] = ++$incInt;

                if(!preg_match('/^[a-z]\\_/', $fieldSchema->Field) && !in_array($fieldSchema->Field, $classVars)) {
                    $this->writeLine("WARNING: Not found in model class - {$model_name}::{$fieldSchema->Field}");
                }
            }

            // Bind the model
            $fakeModel = RougeModelBinder::$singleton->bindModel($fakeData, $model_name);

            $this->validateModel($fakeModel, $arg);
        }
    }

    public function validateModel($model, $input, $memberStack = array()) {
        $modelClass = is_array($model) ? 'array' : get_class($model);

        foreach($model as $k => $v) {
            if((preg_match('/Notification/', $modelClass) === 1 || preg_match('/History/', $modelClass)) && preg_match('/^subject\\_/', $k) === 1) {
                continue;
            }

            if(null === $v) {
                $this->writeLine("Found null member {$k} (member stack: " . implode(', ', $memberStack) . ") with input: {$input}");
                continue;
            }

            if(is_array($v) || is_object($v)) {
                $this->validateModel($v, $input, array_merge($memberStack, array($k)));
                continue;
            }

            continue;
        }
    }
}
