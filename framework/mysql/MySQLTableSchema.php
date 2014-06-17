<?php

class MySQLTableSchema {
    /**
     * @var MySQLFieldSchema[]
     */
    private $field_schemas = array();
    /**
     * @param MySQLFieldSchema[] $initialFieldSchemas
     */
    public function __construct($initialFieldSchemas = array()) {
        $this->field_schemas = $initialFieldSchemas;
    }

    public function insert_field_schema_at(MySQLFieldSchema $fieldSchema, $index = 0) {
        if($index < 0) {
            throw new Exception("Field index out of bounds, {$index} must be greater than or equal to 0.");
        }

        $currentFieldCount = count($this->field_schemas);

        if($index > $currentFieldCount) {
            throw new Exception("Field index out of bounds, {$index} must be less than or equal to {$currentFieldCount}.");
        }

        if($index == $currentFieldCount) {
            $this->append_field_schema($fieldSchema);
            return;
        }

        $this->field_schemas = array_merge(array_slice($this->field_schemas, 0, $index), array($fieldSchema), array_slice($this->field_schemas, $index + 1));
        print_r($this->field_schemas);
        die();
    }

    public function append_field_schema(MySQLFieldSchema $fieldSchema) {
        $this->field_schemas[] = $fieldSchema;
    }

    public function prepend_field_schema(MySQLFieldSchema $fieldSchema) {
        $this->insert_field_schema_at($fieldSchema, 0);
    }
}