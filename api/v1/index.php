<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require '../vendor/autoload.php';
$app = new \Slim\App;

$app->get('/all', function(Request $request, Response $response, array $args){
	echo "allusers";
});


$app->run();
?>