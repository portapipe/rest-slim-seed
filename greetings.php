<?php

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->get('/greetings/:name', function($name) {
    echo "Hello " . $name;
});

$app->run();
?>