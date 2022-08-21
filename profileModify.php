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
    $firstName = sanitize($_POST["FirstName"]);
    $lastName = sanitize($_POST["LastName"]);
    $phone = sanitize($_POST["Phone"]);

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
        $cv = sanitize($_POST["CV"]);
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
        json("Sikertelen módosítás","error",["errors" => $errors]);
    }else{
        setAlert("Sikeres módosítás","success");
        json("Sikeres módosítás","ok",["redirect" => "index.php?page=profile"]);
    }
}
json("Invalid request");