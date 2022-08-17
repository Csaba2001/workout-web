<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");
require_once("User.php");
require_once("Trainer.php");

$user = new User();
$user = User::getCurrentUser();
if(!$user){
    redirect("index.php?page=home");
}

if(isPost() && !empty($_POST)){
    modprofile();
}else{
    try{
        $_POST=json_decode(file_get_contents("php://input"),true);
        modprofile();
    }catch (Exception $e){
        json("Not a POST request");
    }
}

function modprofile(){
    global $dbh;
    $lastName = sanitize($_POST["modifyFirstName"]);
    $firstName = sanitize($_POST["modifyLastName"]);
    $phone = sanitize($_POST["modifyPhone"]);

    $user = new User();
    $user = User::getCurrentUser();
    $user->set($user);
    $user->LastName = $lastName;
    $user->FirstName = $firstName;
    $user->Phone = $phone;
    $user->save();

    $trainerError = null;
    $errors = $user->_errors;

    if(!$user->_errors && $user->isTrainer()){
        $cv = sanitize($_POST["cvText"]);
        $trainer = new Trainer();
        $trainer = Trainer::getFromID($_SESSION["PersonID"]);
        $trainer->CV = $cv;
        $trainer->save();
        if($trainer->_errors){
            $trainerError = $trainer->_errors;
            $errors = array_merge($user->_errors,$trainerError);
        }
    }

    if($errors){
        json(implode("<br>",$errors));
    }else{
        json("Sikeres modositas","ok");
    }
}