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
	global $db_handler;
	try {
		$obj = array(
			"action" => "get_all_users"
		);
		$result = $db_handler->manage_users($obj);
	}
	catch(\Exception $ex) {
		return $response->withJson(array("error" => $ex->getMessage(), 422));
	}
	return $result;
});

//GET: User information by ID
$app->get('/{id}', function(Request $request, Response $response, array $args){
	global $db_handler;
	try {
		$obj = array(
			"action" => "get_user",
			"uid" => $request->getAttribute('id')
		);
		$result = $db_handler->manage_users($obj);
	}
	catch (\Exception $ex) {
		return $response->withJson(array("error" => $ex->getMessage(), 422));
	}
	return $result;
});

//UPDATE: Update profile information
$app->put('/{id}', function(Request $request, Response $response, array $args){
	global $db_handler;
	try {
		$obj = array(
			"action" => "update_profile",
			"uid" => $request->getAttribute('id'),
			"data" => $request->getParsedBody()
		);
		$result = $db_handler->manage_users($obj);
	}
	catch(\Exception $ex) {
		return $response->withJson(array("error" => $ex->getMessage(), 422));
	}
	return $result;
});

//UPDATE: Set user visibility
$app->put('/{id}/visible/{val}', function(Request $request, Response $response, array $args){
	global $db_handler;
	try {
		$obj = array(
			"action" => "isVisible",
			"uid" => $request->getAttribute('id'),
			"isVisible" => $request->getAttribute('val')
		);
		$result = $db_handler->manage_users($obj);
	}
	catch(\Exception $ex) {
		return $response->withJson(array("error" => $ex->getMessage(), 422));
	}
	return $result;
});

//POST: Create user
$app->post('/add', function(Request $request, Response $response, array $args){
	global $db_handler;
	try {
		$obj = array(
			"action" => "create_user",
			"data" => $request->getParsedBody()
		);
		$result = $db_handler->manage_users($obj);
	}
	catch(\Exception $ex) {
		return $response->withJson(array('error' => $ex->getMessage()),422);
	}
	return $result;
});

//POST: Login User
$app->post('/login', function(Request $request, Response $response, array $args){
	global $db_handler;
	try {
		$obj = array(
			"action" => "login",
			"data" => $request->getParsedBody()
		);
		$result = $db_handler->manage_users($obj);
	}
	catch(\Exception $ex) {
		return $response->withJson(array('error' => $ex->getMessage()),422);
	}
	return $result;
});

//POST: Logout User

//DELETE: Remove user
$app->delete('/{id}', function(Request $request, Response $response, array $args){
	global $db_handler;
	try {
		$obj = array(
			"action" => "delete_user",
			"uid" => $request->getAttribute('id')
		);
		$result = $db_handler->manage_users($obj);
	}
	catch(\Exception $ex) {
		return $response->withJson(array('error' => $ex->getMessage()),422);
	}
	return $result;
});

$app->run();
?>

Fetch friends of user
GET: /users/1/friends

Fetch friend requests of user
GET: /users/1/friend-requests

Search user
POST: /users/search

Add friend
POST: /users/1/friends/2

Delete friend
DELETE: /users/1/friends/2

Send friend request
POST: /users/1/friend-requests/2

Delete friend request
DELETE: /users/1/friend-requests/2