<?php
/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */
require 'Slim/Slim.php';
//require 'Slim/Lib/MySql.php'

\Slim\Slim::registerAutoloader();

/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
$app = new \Slim\Slim();

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 */



/**
 * User Routes and Methods: Define the routes meant for the session activity of the user
 *
 * Here are defined the follow requests
 * 
 * 1.- We check if the session is still alive '/livesession/:id/'
 * 2.- The log into the system by checking the credentials '/login'
 * 3.- Session is closed /closesession'
 * 4.- We add a new user '/newuser'
 */


//
$app->post('/login', function() use ($app){      
    //echo 'cool';
    $params = json_decode($app->request->getBody());
    $loginDB = new loginDB();
    $data = $loginDB->getLogin($params);
    unset($loginDB);
    $response = $app->response();
    $response->header('Access-Control-Allow-Origin', '*');
    $response->header('Access-Control-Allow-Credentials', true);      
    $response->write($data);
});

//
$app->post('/livesession/:id', function() use ($app){      
    //echo 'cool';
    $params = json_decode($app->request->getBody());
    $loginDB = new loginDB();
    $data = $loginDB->getLogin($params);
    unset($loginDB);
    $response = $app->response();
    $response->header('Access-Control-Allow-Origin', '*');
    $response->header('Access-Control-Allow-Credentials', true);      
    $response->write($data);
});

//
$app->post('/closesession', function() use ($app){      
    //echo 'cool';
    $params = json_decode($app->request->getBody());
    $loginDB = new loginDB();
    $data = $loginDB->getLogin($params);
    unset($loginDB);
    $response = $app->response();
    $response->header('Access-Control-Allow-Origin', '*');
    $response->header('Access-Control-Allow-Credentials', true);      
    $response->write($data);
});

//
$app->post('/newuser', function() use ($app){      
    //echo 'cool';
    $params = json_decode($app->request->getBody());
    $loginDB = new loginDB();
    $data = $loginDB->getLogin($params);
    unset($loginDB);
    $response = $app->response();
    $response->header('Access-Control-Allow-Origin', '*');
    $response->header('Access-Control-Allow-Credentials', true);      
    $response->write($data);
});





/**
 * Relations Routes and Methods: Defined methods meant for the control of the relations
 * between users.
 *
 * 1.- We check if theres an existing relation between 2 users
 * 2.- Create a relation between 2 users
 * 3.- Delete a relation between 2 users
 */

$app -> get('/relation',          'getRelationStatus');
$app -> post('/addrelation',      'postAddRelation');
$app -> delete('/deleterelation', 'deleteRelation');

//
function getRelationStatus(){
    
}

//
function postAddRelation(){

}

//
function deleteRelation(){

}


/**
 * Messages controller : methods used for regulate the life of them.
 *
 * 1.- Add message personal or to another user
 * 2.- Change status message to DONE
 */

$app -> post('/addmessage',  'postAddMessage');
$app -> put('/changestatus', 'putChangeStatus');

//
function postAddMessage(){

}

//
function putChangeStatus(){

}


/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */ 


/**
* Database connection driver
*
*/

function getConnection(){
    try {
        $dbhost="127.0.0.1";
        $dbuser="root";
        $dbpass="fragmentacion";
        $dbname="cellar";
        $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbh;

    } catch (Exception $e) {
        return $e;
    }
    
}

$app->run();
