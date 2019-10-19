<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../../vendor/autoload.php';
require_once('../../includes/db_connect.php');
require_once('../../includes/db_handler.php');
require_once('../../includes/classes/user.php');

$db = new DB_CONNECT();
$conn = $db->connect("webchat_db");
$db_handler = new User($conn);

$app = new \Slim\App;

//GET: Fetch all users
$app->get('/', function(Request $request, Response $response, array $args){
	echo "Get all users";
});

//GET: User information by ID
$app->get('/{id}', function(Request $request, Response $response, array $args){
	echo "Parameters missing.";
});

//UPDATE: Update user credentials

//UPDATE: Update profile information

//UPDATE: Set user visibility

//POST: Create user
$app->post('/add', function(Request $request, Response $response, array $args){
	global $db_handler;
	try {
		$data = $request->getParsedBody();
		$result = $db_handler->create_new_user($data);
		return $result;
	}
	catch(\Exception $ex) {
		return $response->withJson(array('error' => $ex->getMessage()),422);
	}

});

//POST: Login User

//POST: Logout User

//DELETE: Remove user 


$app->run();
?>

