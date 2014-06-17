<?php

class MySQLTable {
    /**
     * @var string
     */
    public $name;

    /**
     * @var MySQLDatabase
     */
    public $database;

    /**
     * @var MySQLTableSchema
     */
    public $schema;

    /**
     * @var MySQLFieldSchema
     */
    public $primary_key = null;

    public function __construct($name, MySQLDatabase $database, MySQLTableSchema $schema, $primaryKey = false) {
        $this->database = $database;
        $this->name = $name;
        $this->schema = $schema;
        $this->primary_key = $primaryKey;
    }

    /**
     * The simplest, safest, and slowest way to save a record.
     * @param $record
     * @return boolean
     */
    public function save($record, $savableMethod = null) {
        $savableMethod = $savableMethod === null ? 'asSavableModel' : $savableMethod;
        $record = is_object($record) ? $record : new MySQLRecord($record);

        if(method_exists($record, $savableMethod)) {
            $record = (object)$record->$savableMethod();
        }

        // If there is a primary key, we should check if there is already an entry for this record.
        if($this->primary_key !== false && isset($record->{$this->primary_key->Field})) {
            $primaryKey = $this->primary_key->Field;

            $primaryValueEncoded = $this->database->encode_value($record->{$primaryKey});

            $result = $this->database->query("SELECT COUNT(*) FROM `{$this->name}` WHERE `{$this->name}`.`{$primaryKey}` = $primaryValueEncoded");

            $row = $result->fetch_assoc();

            // We can update the existing record.
            if($row['COUNT(*)']) {
                $query = "UPDATE `{$this->name}` SET ";

                $first = true;
                foreach($record as $field => $value) {
                    // This won't need updating.
                    if($field == $primaryKey) {
                        continue;
                    }

                    // This field doesn't exist in the schema.
                    if(!count(array_filter($this->schema, function($field_schema) use($field) {
                        return $field_schema->Field == $field;
                    }))) {
                        continue;
                    }

                    $query .= !$first ? ',' : '';

                    $query .= "`{$this->name}`.`{$field}` = " . $this->database->encode_value($value);

                    $first = false;
                }

                $query .= " WHERE `{$this->name}`.`{$primaryKey}` = {$primaryValueEncoded}";

                $this->database->query($query);

                return true;
            }
        }

        // At this point we know should perform an INSERT
        $query = "INSERT INTO `{$this->name}` (";

        $first = true;
        foreach($this->schema as $schema) {
            $query .= !$first ? ',' : '';

            $query .= "`{$this->name}`.`{$schema->Field}`";

            $first = false;
        }

        $query .= ") VALUES (";

        $first = true;
        foreach($this->schema as $schema) {
            $query .= !$first ? ',' : '';

            $query .= $this->database->encode_value(
                isset($record->{$schema->Field}) ? $record->{$schema->Field} :
                    ($schema->Null ? null : '')
            );

            $first = false;
        }

        $query .= ")";

        return $this->database->query($query) ? true : false;
    }

    public function beginFluentSelectQuery($shortname = null) {
        $fluentSelectQuery = new MySQLFluentSelectQuery();

        $fluentSelectQuery->from($this, $shortname);

        return $fluentSelectQuery;
    }

    public function findBy($column, $value, $modelType = null) {
        $query = "SELECT ";

        $first = true;
        foreach($this->schema as $schema) {
            $query .= !$first ? ',' : '';

            $query .= "`{$this->name}`.`{$schema->Field}`";

            $first = false;
        }

        $query .= " FROM `{$this->name}` WHERE `{$this->name}`.`{$column}` = " . $this->database->encode_value($value);

        $result = $this->database->query($query);

        $records = array();

        while($row = $result->fetch_object()) {
            $recordModel = $this->database->construct_model_from_row_object($row, $modelType);

            $records[] = $recordModel;
        }

        return $records;
    }

    public function findByEx($columns = array(), $order = array(), $modelType = null) {
        $query = "SELECT ";

        $first = true;
        foreach($this->schema as $schema) {
            $query .= !$first ? ',' : '';

            $query .= "`{$this->name}`.`{$schema->Field}`";

            $first = false;
        }

        $query .= " FROM `{$this->name}` WHERE";

        $first = true;
        foreach($columns as $column => $value) {
            $operation = is_array($value) && isset($value['operation']) ? $value['operation'] : '=';

            $encoded = $this->database->encode_value(is_array($value) ? $value['value'] : $value);

            $query .= !$first ? ' AND' : '';

            $query .= " `{$this->name}`.`$column` $operation $encoded";

            $first = false;
        }

        if(!empty($order)) {
            $query .= " ORDER BY";

            $first = true;
            foreach($order as $column => $direction) {
                $query .= !$first ? ',' : '';

                $query .= " `{$this->name}`.`$column` $direction";

                $first = false;
            }
        }

        $result = $this->database->query($query);

        $records = array();

        while($row = $result->fetch_object()) {
            $records[] = $this->database->construct_model_from_row_object($row, $modelType);
        }

        return $records;
    }

    public function firstBy($column, $value, $modelType = null) {
        $query = "SELECT ";

        $first = true;
        foreach($this->schema as $schema) {
            $query .= !$first ? ',' : '';

            $query .= "`{$this->name}`.`{$schema->Field}`";

            $first = false;
        }

        $query .= " FROM `{$this->name}` WHERE `{$this->name}`.`{$column}` = " . $this->database->encode_value($value) .
            " LIMIT 1";

        $result = $this->database->query($query);

        $object = $result->fetch_object();

        if(null === $object) {
            return null;
        }

        return $this->database->construct_model_from_row_object($object, $modelType);
    }

    public function firstByEx($columns = array(), $order = array(), $modelType = null) {
        $query = "SELECT ";

        $first = true;
        foreach($this->schema as $schema) {
            $query .= !$first ? ',' : '';

            $query .= "`{$this->name}`.`{$schema->Field}`";

            $first = false;
        }

        $query .= " FROM `{$this->name}` WHERE ";

        $first = true;
        foreach($columns as $column => $value) {
            $operation = is_array($value) && isset($value['operation']) ? $value['operation'] : '=';

            $encoded = $this->database->encode_value(is_array($value) ? $value['value'] : $value);

            $query .= !$first ? ' AND' : '';

            $query .= " `{$this->name}`.`$column` $operation $encoded";

            $first = false;
        }

        if(!empty($order)) {
            $query .= " ORDER BY";

            $first = true;
            foreach($order as $column => $direction) {
                $query .= !$first ? ',' : '';

                $query .= " `{$this->name}`.`$column` $direction";

                $first = false;
            }
        }

        $query .= " LIMIT 1";

        $result = $this->database->query($query);

        $object = $result->fetch_object();

        if(null === $object) {
            return null;
        }

        return $this->database->construct_model_from_row_object($object, $modelType);
    }

    public function all($modelType = null) {
        $query = "SELECT ";

        $first = true;
        foreach($this->schema as $schema) {
            $query .= !$first ? ',' : '';

            $query .= "`{$this->name}`.`{$schema->Field}`";

            $first = false;
        }

        $query .= " FROM `{$this->name}`";

        $result = $this->database->query($query);

        $records = array();

        while($row = $result->fetch_object()) {
            $records[] = $this->database->construct_model_from_row_object($row, $modelType);
        }

        return $records;
    }
}