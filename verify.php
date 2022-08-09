<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");
global $dbh;

if(isset($_SESSION['Email'])){
    redirect("index.php?page=home");
}else{
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
            $sql = "SELECT Verified, VerifyCode, RegistrationExpires FROM persons WHERE Email = :email;";
            $query = $dbh->prepare($sql);
            $query->bindParam(":email",$email);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if($result){
                if($code !== $result["VerifyCode"]){
                    die("Ervenytelen kod");
                }

                if($result["Verified"] !== "pending"){
                    die("Mar visszaigazolta a regisztraciot");
                }
                $now = new DateTime("now");
                $then = new DateTime($result["RegistrationExpires"]);
                if($now > $then){
                    die("Az ideje lejart, kerjuk regisztraljon ujra");
                }

                $sql = "UPDATE persons SET Verified = 'verified' WHERE Email = :email;";
                $query = $dbh->prepare($sql);
                $query->bindParam(":email",$email);
                if($query->execute()){
                    die("Sikeresen visszaigazolta a regisztraciot. <br><a href='index.php?page=home'>Vissza a fooldalra</a>");
                }else{
                    die("Hiba tortent, probalja ujra");
                }
            }else{
                die("A regisztraciojat toroltek");
            }
        }catch(PDOException $e){
            die("SQL hiba tortent: ".$e->getMessage());
        }
    }else{
        redirect("index.php?page=home");
    }
}
redirect("index.php?page=home");