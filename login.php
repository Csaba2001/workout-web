<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");
require_once("User.php");
require_once("Trainer.php");

$currentUser = new User();
$currentUser = User::getCurrentUser();
if($currentUser){
    $currentUser->logout();
    redirect("index.php?page=home");
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
    $email = sanitize($_POST["Email"]);
    $password = sanitize($_POST["Hash"]);

    $user = new User();
    $user->Email = $email;
    $user->Password = $password;

    if($user->login()){
        if($user->Rank === "trainer"){
            $trainer = new Trainer();
            $trainer = Trainer::getFromEmail($email);
            if($trainer->approval === "pending"){
                $user->logout();
                json("A felhasználó nincs engedélyezve");
            }
        }
        setAlert("Üdvözöljük ".$user->displayName(),"success");
        json("Sikeres bejelentkezés", "ok", ["redirect" => "index.php?page=home"]);
    }else{
        json("Sikertelen bejelentkezés","error",["errors" => $user->_errors]);
    }
}
json("Empty request");