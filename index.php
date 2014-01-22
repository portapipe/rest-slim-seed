<?php

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
$app->add(new \CORSEnablerMiddleware());
$app->add(new \LoggerMiddleware());

$db = new SQLite3('SlimRestSeed.db');

$app->get('/user/list/', function() use ($db) {
    $results = $db->query('SELECT * FROM User');

    $users = array();

    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        array_push($users, $row);
    }

    echo json_encode($users);
});

$app->get('/user/get/:id', function($id) use ($app,$db) {

    $user = $db->querySingle('SELECT * FROM User WHERE id='.$id, true);
    
    if ($user) {
        echo json_encode($user);
    } else {
        $app->response()->status(404);
        echo "no user found";
    }
});

$app->post('/user/create/', function() use($app,$db) {
    $user = json_decode($app->request()->getBody());
    
    $sql = "INSERT INTO User VALUES ( NULL , '$user->email' , '$user->name' , '$user->surname' , '$user->sex' , $user->years)";
    
    $db->exec($sql);
    
    $user->id = $db->lastInsertRowID();
    
    echo json_encode($user);
    
    
});

$app->put('/user/update/', function() use($app,$db) {
    
    $user = json_decode($app->request()->getBody());
    
    $sql = "UPDATE 'User' SET email='$user->email' , name='$user->name' ,surname='$user->surname' ,sex='$user->sex' , years=$user->years WHERE id = $user->id";
    
    $db->exec($sql);
    
    echo json_encode($user);
});

$app->delete('/user/delete/:id', function ($id) use($db) {
    $db->exec("DELETE FROM User WHERE id=$id");
});

$app->options('/(:name+)', function() use($app) {
    $response = $app->response();

    $response->header('Access-Control-Allow-Origin', '*');
    $response->header('Access-Control-Allow-Credentials', 'true');
    $response->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, X-authentication, X-client, X-MICROTIME, X-HASH');
    $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

    $response->status(200);
});

$app->run();
?>