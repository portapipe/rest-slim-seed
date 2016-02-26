<?php
/* 
	In caso di test in locale il browser potrebbe dare problemi riguardo l'origine e altri 'Access-Control-Allow' qualcosa.
	Con le linee seguenti viene permesso l'accesso ad un IP (da modificare) e l'invio di una chiave di Autorizzazione tramite header
	Togli i tag commento per utilizzarle!
*/
/*
header('Access-Control-Allow-Origin: http://0.0.0.0:8100');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Authorization');
*/

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
$app->add(new \CORSEnablerMiddleware());
$app->add(new \LoggerMiddleware());

//Usa authRequest($app) nelle route che vuoi proteggere con chiave di autorizzazione!
function authRequest($app){
	if(getallheaders()['Authorization']!="Basic 12345678987654321portapipe_wordpress_com"){
	    $app->status(401);
	    header("HTTP/1.1 401 Unauthorized");
		echo json_encode(array("auth"=>"false"));
		die();
		return false;
	}
	return true;
}

$db = new SQLite3('SlimRestSeed.db');

$app->get('/user/list/', function() use ($app,$db) {
	//Esempio utilizzo auth
	//authRequest($app);
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
