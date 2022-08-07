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
    register();
}else{
    try {
        $_POST = json_decode(file_get_contents("php://input"),true);
        register();
    }catch(Exception $e) {
        json("Not a POST request");
    }
}
function register(){
    $email = sanitize($_POST["registerEmail"]);
    $password = sanitize($_POST["registerPassword"]);
    $firstname = sanitize($_POST["registerFirstName"]);
    $lastname = sanitize($_POST["registerLastName"]);
    $phone = sanitize($_POST["registerPhone"]);
    $passwordConfirm = sanitize($_POST["registerPasswordConfirm"]);
    $userType = sanitize($_POST["registerUserType"]);

    if(!$firstname || strlen($firstname) < 3){
        json("First Name too short");
    }
    if(strlen($firstname) > 30){
        json("First Name too long");
    }

    if(!$lastname || strlen($lastname) < 3){
        json("Last Name too short");
    }
    if(strlen($lastname) > 30){
        json("Last Name too long");
    }

    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        json("Not an email");
    }

    if(!preg_match('/^(\+?\d{2,3})?(0+?)?(\d{9})$/',$phone)){
        json("Not a valid phone number");
    }

    if(!$password || strlen($password) < 3){
        json("Password too short");
    }
    if(strlen($password) > 12){
        json("Password too long");
    }
    if($password !== $passwordConfirm){
        json("Passwords don't match");
    }

    $hash = password_hash($password,PASSWORD_BCRYPT);
    $verifycode = bin2hex(random_bytes(10));
    $now = new DateTime("now");
    $now->modify("+1 day");
    $registrationexpires = $now->format("Y-m-d H:i:s");

    $rank = "";
    if($userType === "trainer"){
        $rank = "trainer";
    }else{
        $rank = "user";
    }

    try {
        $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME.";charset=utf8", DB_USER, DB_PASS);

        $sql = "INSERT INTO persons (LastName, FirstName, Phone, Email, Hash, VerifyCode, RegistrationExpires, Rank) VALUES (:lastname,:firstname, :phone, :email, :hash, :verifycode, :registrationexpires, :rank);";
        $query = $dbh->prepare($sql);
        $query->bindParam(":lastname", $firstname);
        $query->bindParam(":firstname", $lastname);
        $query->bindParam(":phone", $phone);
        $query->bindParam(":email", $email);
        $query->bindParam(":hash", $hash);
        $query->bindParam(":verifycode", $verifycode);
        $query->bindParam(":registrationexpires", $registrationexpires);
        $query->bindParam(":rank", $rank);

        if($query->execute()){
            $lastID = $dbh->lastInsertId();
            $verifyURL = 'http://'.HOST.'/verify.php?code='.$verifycode.'&email='.$email;
            $message = '<p>Köszönjük, hogy regisztrált</p>
                        <p>Kérjük kattintson a linkre, hogy a regisztrációja véglegesüljon: </p>
                        <a href="'.$verifyURL.'">'.$verifyURL.'</a>';
            if($userType !== "trainer"){ //user
                if(sendMail($email, "Regisztráció igazolás", $message)){
                    json("Sikeres regisztráció", "ok");
                }else{
                    json("Sikeretelen regisztráció, probálja újra");
                }
            }else{ //trainer
                $message .= '<p>A regisztracióját egy admin fogja elbirálni 24 órán belül.</p>
                             <p>Kérjük legyen türelemmel.</p>';
                $sql = "INSERT INTO trainers (TrainerID) VALUES (:lastID);";
                $query->prepare($sql);
                $query->bindParam(":lastID",$lastID);
                if($query->execute()){
                    if(sendMail($email, "Regisztracio igazolas", $message)){
                        json("Sikeres regisztracio", "ok");
                    }else{
                        json("Sikeretelen regisztráció, probálja újra");
                    }
                }else{
                    json("SQL hiba tortent");
                }
            }
        }else{
            json("SQL hiba tortent");
        }
    }catch(PDOException $e) {
        $existingkey = "Integrity constraint violation: 1062 Duplicate entry";
        if (strpos($e->getMessage(), $existingkey) !== FALSE) {
            json("Az email cim mar foglalt");
        }else {
            json("SQL hiba tortent" . $e->getMessage());
        }
    }
}
json("Empty request");