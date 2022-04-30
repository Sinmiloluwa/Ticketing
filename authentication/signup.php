<?php

if (isset($_POST["submit"])) {

    // Grab form data
    $uid = $_POST["uid"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $pwd = $_POST["password"];

    // Instantiate the AuthController and User class
    include "../classes/Db.php";
    include "../classes/User.php";
    include "../classes/AuthController.php";

    $signup = new AuthController($uid, $pwd, $email, $name);

    // User signup
    $signup->createUser();

    echo 'Submitted';
}