<?php

class MySQLFieldSchema {
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $type;

    /**
     * @var boolean
     */
    private $allow_null;

    /**
     * @var string
     */
    private $key;

    /**
     * @var null|string
     */
    private $default;

    /**
     * @var string
     */
    private $extra;

    public function __construct($field, $type, $allowNull, $key, $default, $extra) {
        $this->field = $field;
        $this->type = $type;
        $this->allow_null = $allowNull;
        $this->key = $key;
        $this->default = $default;
        $this->extra = $extra;
    }

    /**
     * @return string
     */
    public function get_field() {
        return $this->field;
    }

    /**
     * @param string $field
     */
    public function set_field($field) {
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function get_type() {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function set_type($type) {
        $this->type = $type;
    }

    /**
     * @return boolean
     */
    public function get_allow_null() {
        return $this->allow_null;
    }

    /**
     * @param boolean $allowNull
     */
    public function set_allow_null($allowNull) {
        $this->allow_null = $allowNull;
    }

    /**
     * @return string
     */
    public function get_key() {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function set_key($key) {
        $this->key = $key;
    }

    /**
     * @return null|string
     */
    public function get_default() {
        return $this->default;
    }

    /**
     * @param null|string $default
     */
    public function set_default($default) {
        $this->default = $default;
    }

    /**
     * @return string
     */
    public function get_extra() {
        return $this->extra;
    }

    /**
     * @param string $extra
     */
    public function set_extra($extra) {
        $this->extra = $extra;
    }
}