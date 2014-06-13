<?php
$config = array(
    'ignore_extensions' => true,
    'routes' => array(
        new Route('/', 'HomeResource'),
        new Route('/:name', 'WelcomeResource')
    ),
    'not_found_response' => 'NotFoundResponse'
);