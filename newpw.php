<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");

global $dbh;

if(isLoggedIn()){
    redirect("index.php?page=home");
}
if(isGet() && !empty($_GET)){
    $code = sanitize($_GET["code"]);
    $email = sanitize($_GET["email"]);

    if(strlen($code) != 20){
        redirect("index.php?page=home");
    }
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        redirect("index.php?page=home");
    }

    try {
        $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME.";charset=utf8", DB_USER, DB_PASS);
        $sql = "SELECT CodePassword, NewPasswordExpires, NewPassword, Hash FROM persons WHERE Email = :email;";
        $query = $dbh->prepare($sql);
        $query->bindParam(":email",$email);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if($result){
            if(!$result["CodePassword"] or !$result["NewPasswordExpires"] or !$result["NewPassword"]){
                die("Ervenytelen kod");
            }
            if($code !== $result["CodePassword"]){
                die("Ervenytelen kod");
            }
            $now = new DateTime("now");
            $then = new DateTime($result["NewPasswordExpires"]);
            if($now > $then){
                $sql = "UPDATE persons SET CodePassword = NULL, NewPasswordExpires = NULL, NewPassword = NULL WHERE Email = :email;";
                $query = $dbh->prepare($sql);
                $query->bindParam(":email",$email);
                $query->execute();
                die("A kerelem lejart, kerjuk generaljon ujat");
            }

            $newpassword = $result["NewPassword"];

            $sql = "UPDATE persons SET Hash = :newpassword, CodePassword = NULL, NewPasswordExpires = NULL, NewPassword = NULL WHERE Email = :email;";
            $query = $dbh->prepare($sql);
            $query->bindParam(":newpassword", $newpassword);
            $query->bindParam(":email",$email);
            if($query->execute()){
                die("A jelszava sikeresen megvaltozott <br><a href='index.php?page=home'>Vissza a fooldalra</a>");
            }else{
                die("Hiba tortent, probalja ujra");
            }
        }else{
            die("Hibas email cim");
        }
    }catch(PDOException $e){
        die("SQL hiba tortent: ".$e->getMessage());
    }
}else{
    redirect("index.php?page=home");
}

redirect("index.php?page=home");