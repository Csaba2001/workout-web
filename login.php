<?php
session_start();
const SECRET = "eperfa28ha3";
require_once("db_config.php");
include_once("functions.php");

if(isset($_SESSION['Email'])){
    unset($_SESSION);
    session_destroy();
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

    try {
        $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME.";charset=utf8", DB_USER, DB_PASS);

        $sql = "SELECT * FROM Persons WHERE Email=? Limit 1";
        $query = $dbh->prepare($sql);
        $query->bindParam(1, $email);
        $query->execute();
        $results = $query->fetch(PDO::FETCH_ASSOC);
        if($results){
            if(password_verify($password, $results['Hash'])){
                $_SESSION = $results;
                json("Successful login", "ok", ["redirect" => "index.php?page=home"]); //orulj neki (: szassz
            }else{
                json("Buta fasz rossz jelszo"); //hmmm, hühh, kiugraszazablakonkitorlom
            }
        }else{
            json("Cannot find user ".$email); //? nem tuntem e!
        }
    }catch(PDOException $e) {
        json("SQL hiba tortent".$e->getMessage());
    }
}
json("Empty request");