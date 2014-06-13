<?php

class MySQLModel extends Model {
    public $table_name = null;
    public $view_name = null;

    /**
     * This function is called right before deleting
     */
    public function before_deleting_model() {}
    public function after_deleting_model() {}

    /**
     * This function is called right before updating a database model.
     */
    public function before_updating_model() {}

    /**
     * This function is called right after updating a database model.
     */
    public function after_updating_model() {}

    /**
     * This function should return the data used when updating a database model.
     * @return object
     */
    public function query_updating_model() {
        return $this;
    }

    /**
     * This function is called right before creating a database model.
     */
    public function before_creating_model() {}

    /**
     * This function is called right after creating a database model.
     */
    public function after_creating_model() {}

    /**
     * This function should return the data used when creating a database model.
     * @return object
     */
    public function query_creating_model() {
        $primaryKey = $this->get_primary_key();
        $data = (object)$this;

        if(isset($data->$primaryKey)) {
            unset($data->$primaryKey);
        }

        return $data;
    }
}