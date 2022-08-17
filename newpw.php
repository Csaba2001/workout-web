<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");
require_once("User.php");

global $dbh;

if(isGet() && !empty($_GET)){
    $code = sanitize($_GET["code"]);
    $email = sanitize($_GET["email"]);

    if(strlen($code) != 20){
        redirect("index.php?page=home");
    }
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        redirect("index.php?page=home");
    }

    $user = new User();
    $user = User::getFromEmail($email);
    if(!$user){
        redirect("index.php?page=home");
    }
    $user->set($user);
    if(!$user->CodePassword or !$user->NewPasswordExpires or !$user->NewPassword){
        setAlert("Ervenytelen kod");
        redirect("index.php?page=home");
    }
    if($code !== $user->CodePassword){
        setAlert("Ervenytelen kod");
        redirect("index.php?page=home");
    }

    $now = new DateTime("now");
    $then = new DateTime($user->NewPasswordExpires);

    if($now > $then){
        $user->CodePassword = null;
        $user->NewPasswordExpires = null;
        $user->NewPassword = null;
        $user->save();
        setAlert("A kerelem lejart, kerjuk generaljon ujat");
        redirect("index.php?page=home");
    }

    $user->Hash = $user->NewPassword;
    $user->CodePassword = null;
    $user->NewPasswordExpires = null;
    $user->NewPassword = null;

    if($user->save()){
        $currentUser = new User();
        $currentUser = User::getCurrentUser();
        if($currentUser){
            $currentUser->logout();
        }
        setAlert("A jelszava sikeresen megvaltozott, mostmar bejelentkezhet az uj jelszavaval","success");
        redirect("index.php?page=home");
    }else{
        setAlert("Hiba tortent, probalja ujra");
        redirect("index.php?page=home");
    }
}else{
    redirect("index.php?page=home");
}

redirect("index.php?page=home");