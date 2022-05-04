<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/Db.php';
include_once '../models/Event.php';
include_once '../models/User.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
$api = $_SERVER['REQUEST_METHOD'];

$database = new Db();
$db = $database->connect();

// Instantiate Models
$event = new Event($db);
$user = new User($db);

if ($api === 'GET') {
    if ($uri[1] === 'read' ) {
        $event->read();
    }
    
    if ($uri[1] === 'show') {
        $event->show();
    }
    
}
elseif ($api === 'POST') {
    if ($uri[1] === 'createEvent') {
        $event->create();
    }
    if ($uri[1] === 'login') {
        $user->getUser();
    }

    if ($uri[1] === 'createUser') {
        $user->create();
    }

    if ($uri[1] === 'pay') {
        $event->pay();
    }
}









