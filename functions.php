<?php
@define("SECRET", "eperfa28ha3");
require_once("db_config.php");
function redirect($URL){
    header("Location: $URL");
    echo "<script>window.location.href = '$URL';</script>";
    die();
}
function sanitize($str){
    $str = stripslashes($str);
    $str = trim($str);
    return $str = htmlspecialchars($str);
}
function json($str, $type = "error", $options = []){
    die(json_encode([
        "type" => $type,
        "message" => $str,
        "options" => $options
    ]));
}
function isPost(){
    return ($_SERVER["REQUEST_METHOD"] === "POST");
}
function isGet(){
    return ($_SERVER["REQUEST_METHOD"] === "GET");
}
function sendMail($to, $subject, $message){
    $headers = array(
        'From' => 'noreply@'.HOST,
        'X-Mailer' => 'PHP',
        'Content-Type' => 'text/html; charset=utf-8'
    );
    if(mail($to, $subject, $message, $headers)){
        return true;
    }else{
        return false;
    }
}
function isTrainer(){
    return !empty($_SESSION) && $_SESSION["Rank"] === 'trainer';
}
function isUser(){
    return !empty($_SESSION) && $_SESSION["Rank"] === 'user';
}
function isAdmin(){
    return !empty($_SESSION) && $_SESSION["Rank"] === 'admin';
}
function isLoggedIn(){
    return isTrainer() or isUser() or isAdmin();
}
function logout(){
    unset($_SESSION);
    session_destroy();
    redirect("index.php?page=home");
}