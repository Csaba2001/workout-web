<?php
@define("SECRET", "eperfa28ha3");
require_once("db_config.php");
function redirect($URL){
    header("Location: $URL");
    echo "<script>window.location.href = '$URL';</script>";
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