<?php
include "vendor/autoload.class.php";
include "classes/Db.php";
include "classes/User.php";
include "classes/AuthController.php";
include "classes/LoginController.php";



header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
$api = $_SERVER['REQUEST_METHOD'];


if($api == 'POST') {
    if ($uri[1] === 'signup') {
        // Grab form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $pwd = $_POST["password"];
    $checkPwd = $_POST["confirm_password"];

    $signup = new AuthController($pwd, $email, $name, $checkPwd);

    // User signup
    $signup->createUser();
    }
    if ($uri[1] == 'login') {
        // Get login data
        $email = $_POST['email'];
        $pwd = $_POST['password'];

        $login = new LoginController($email, $pwd);

        $login->loginUser();
    }
    
}

// if ($uri[1] !== 'signup') {
//     header("HTTP/1.1 404 Not Found");
//     exit();
// }



// $userId = null;
// if (isset($uri[2])) {
//     $userId = (int) $uri[2];
// }





