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
                    setAlert("Érvénytelen kód");
                    redirect("index.php?page=home");
                }

                if($result["Verified"] !== "pending"){
                    setAlert("Már visszaigazolta a regisztrációt");
                    redirect("index.php?page=home");
                }
                $now = new DateTime("now");
                $then = new DateTime($result["RegistrationExpires"]);
                if($now > $then){
                    setAlert("Az ideje lejárt, kérjük regisztráljon újra");
                    redirect("index.php?page=home");
                }

                $sql = "UPDATE persons SET Verified = 'verified' WHERE Email = :email;";
                $query = $dbh->prepare($sql);
                $query->bindParam(":email",$email);
                if($query->execute()){
                    setAlert("Sikeresen visszaigazolta a regisztrációt","success");
                    redirect("index.php?page=home");
                }else{
                    setAlert("Hiba történt, próbálja újra");
                    redirect("index.php?page=home");
                }
            }else{
                setAlert("A regisztrációját törölték");
                redirect("index.php?page=home");
            }
        }catch(PDOException $e){
            setAlert("SQL hiba történt: ".$e->getMessage());
            redirect("index.php?page=home");
        }
    }else{
        redirect("index.php?page=home");
    }
}
redirect("index.php?page=home");