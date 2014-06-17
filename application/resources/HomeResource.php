<?php

class HomeResource extends AppResource {
    public function execute() {
        db()->get_table(UserModel::$table_name);
        return new ViewResponse('HomePage', array(
            'x' => 4
        ));
    }
}