<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");

if(isLoggedIn()){
    logout();
}
if(isPost() && !empty($_POST)){
    login();
}else{
    try {
        $_POST = json_decode(file_get_contents("php://input"),true);
        login();
    }catch(Exception $e) {
        json("Not a POST request");
    }
}
function login(){
    $email = sanitize($_POST["loginEmail"]);
    $password = sanitize($_POST["loginPassword"]);

    if(!$email || strlen($email) < 5){
        json("Email too short");
    }
    if(!$password || strlen($password) < 3){
        json("Password too short");
    }
    if(strlen($password) > 12){
        json("Password too long");
    }
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        json("Not an email");
    }
}
json("Empty request");