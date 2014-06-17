<?php

class UserModel extends MySQLModel {
    public static $table_name = 'catering_users';
    public static $view_name = 'catering_user_view';

    public $id;
    public $first_name;
    public $last_name;
}