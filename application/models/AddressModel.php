<?php

class AddressModel extends MySQLModel {
    public $table_name = 'catering_addresses';
    public $view_name = 'catering_address_view';

    public $id;

    public $user_id;

    /**
     * @var UserModel $user_model
     */
    public $user_model;

    public $name;
    public $zip_code;
    public $address_line1;
    public $address_line2;
}