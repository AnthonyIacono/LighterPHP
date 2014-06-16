<?php
$config = array(
    'ignore_extensions' => true,
    'routes' => array(
        new Route('/', 'HomeResource'),
        new Route('/something/:name/*', 'WildcardResource'),
        new Route('/someform', 'SomeformResource')
    ),
    'not_found_response' => 'NotFoundResponse'
);