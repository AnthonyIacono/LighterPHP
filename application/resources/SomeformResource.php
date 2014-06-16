<?php

class SomeformResource extends AppResource {
    public function execute()
    {
        return new ViewResponse('SomeformView', array(

        ));
    }
}