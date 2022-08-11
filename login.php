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

    try {
        global $dbh;
        $sql = "SELECT * FROM persons WHERE Email = ? LIMIT 1;";
        $query = $dbh->prepare($sql);
        $query->bindParam(1, $email);
        $query->execute();
        $results = $query->fetch(PDO::FETCH_ASSOC);
        if($results){
            if($results["Rank"] === "trainer"){
                $sql = "SELECT * FROM trainers WHERE TrainerID = ?;";
                $query = $dbh->prepare($sql);
                $query->bindParam(1,$results["PersonID"]);
                $query->execute();
                $trainerData = $query->fetch(PDO::FETCH_ASSOC);
                if($trainerData["approval"] === "pending"){
                    json("Az edzo profilja meg nem lett elbiralva, kerjuk legyen turelmes.");
                }
            }
            if(password_verify($password, $results['Hash'])){
                if($results["Verified"] !== "verified"){
                    json("A felhasznalo nincs visszaigazolva kerjuk nezze meg az email fiokjat");
                }
                if($results["Status"] !== "active"){
                    json("A felhasznalo fiokja tiltva van");
                }
                $sql = "UPDATE persons SET NewPassword = NULL, NewPasswordExpires = NULL, CodePassword = NULL WHERE Email = :email;";
                $query = $dbh->prepare($sql);
                $query->bindParam(":email",$email);
                $query->execute();
                $_SESSION = $results;
                json("Successful login", "ok", ["redirect" => "index.php?page=home"]); //orulj neki (: szassz
            }else{
                json("Rossz jelszo"); //hmmm, hühh, kiugraszazablakonkitorlom
            }
        }else{
            json("Cannot find user ".$email); //? nem tuntem e!
        }
    }catch(PDOException $e) {
        json("SQL hiba tortent: ".$e->getMessage());
    }
}
json("Empty request");