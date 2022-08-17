<?php
@session_start();
require_once("functions.php");
require_once("User.php");
require_once("Trainer.php");

$user = new User();
$user = User::getCurrentUser();
if($user){
    redirect("index.php?page=home");
}
if(isPost() && !empty($_POST)){
    register();
}else{
    try {
        $_POST = json_decode(file_get_contents("php://input"),true);
        register();
    }catch(Exception $e) {
        json("Not a POST request");
    }
}
global $dbh;

function register(){
    $email = sanitize($_POST["registerEmail"]);
    $password = sanitize($_POST["registerPassword"]);
    $firstname = sanitize($_POST["registerFirstName"]);
    $lastname = sanitize($_POST["registerLastName"]);
    $phone = sanitize($_POST["registerPhone"]);
    $passwordConfirm = sanitize($_POST["registerPasswordConfirm"]);
    $userType = sanitize($_POST["registerUserType"]);

    $user = new User();
    $user->Email = $email;
    $user->Password = $password;
    $user->PasswordConfirm = $passwordConfirm;
    $user->FirstName = $firstname;
    $user->LastName = $lastname;
    $user->Phone = $phone;
    $user->Rank = $userType ? "trainer" : "user";

    $trainerError = null;
    if($user->Rank === "trainer"){
        $trainer = new Trainer();
        $cv = sanitize($_POST["CV"]);
        $trainer->validateCV($cv);
        $trainerError = $trainer->_errors;
        $user->CV = $cv;
    }

    if($user->register()){
        json("Sikeres regisztráció", "ok");
    }else{
        $errors = $user->_errors;
        if($trainerError){
            $errors = array_merge($user->_errors,$trainerError);
        }
        json(implode("<br>",$errors));
    }
}
json("Empty request");