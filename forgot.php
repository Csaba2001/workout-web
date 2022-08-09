<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");

if(isLoggedIn()) {
    logout();
}

if (isPost() && !empty($_POST)) {
    resetPassword();
} else {
    try {
        $_POST = json_decode(file_get_contents("php://input"), true);
        resetPassword();
    } catch (Exception $e) {
        json("Not a POST request");
    }
}

function resetPassword(){
    global $dbh;

    $email = sanitize($_POST["forgotEmail"]);
    $newPassword = sanitize($_POST["forgotPassword"]);
    $newPasswordConfirm = sanitize($_POST["forgotPasswordConfirm"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        json("Helytelen email");
    }

    if(!$newPassword || strlen($newPassword) < 3){
        json("Password too short");
    }
    if(strlen($newPassword) > 12){
        json("Password too long");
    }
    if($newPassword !== $newPasswordConfirm){
        json("Passwords don't match");
    }

    try {
        $sql = "SELECT * FROM persons WHERE Email = ? LIMIT 1;";
        $query = $dbh->prepare($sql);
        $query->bindParam(1, $email);
        $query->execute();
        $results = $query->fetch(PDO::FETCH_ASSOC);
        if ($results) {
            if($results["Verified"] === "pending"){
                json("A regisztracioja nincs visszaigazolva. Kerjuk elobb igazolja vissza.");
            }
            if($results["Status"] === "banned"){
                json("A felhasznalo fiokja le van tiltva. Nem kerhet uj jelszot.");
            }
            $now = new DateTime("now");
            $then = ($results["NewPasswordExpires"]) ? new DateTime($results["NewPasswordExpires"]) : null;
            if($results["NewPassword"] && $then){
                if($now > $then){
                    $sql = "UPDATE persons SET NewPassword = NULL, CodePassword = NULL, NewPasswordExpires = NULL WHERE Email = :email;";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(":email",$email);
                    $query->execute();
                    json("Az elozo jelszo kerelem lejart, ezert toroltuk, generaljon ujat a Kuldes gombra kattintva.");
                }else{
                    json("Mar kervenyezett elfelejtett jelszot az emult 24 oraban.");
                }
            }else{
                $passwordHash = password_hash($newPassword,PASSWORD_DEFAULT);
                $codepassword = bin2hex(random_bytes(10));
                $now->modify("+1 day");
                $newpasswordexpires = $now->format("Y-m-d H:i:s");
                $sql = "UPDATE persons SET NewPassword = :newpassword, CodePassword = :codepassword, NewPasswordExpires = :newpasswordexpires WHERE Email = :email;";
                $query = $dbh->prepare($sql);
                $query->bindParam(":newpassword",$passwordHash);
                $query->bindParam(":codepassword",$codepassword);
                $query->bindParam(":newpasswordexpires",$newpasswordexpires);
                $query->bindParam(":email",$email);

                $verifyURL = 'http://'.HOST.'/newpw.php?code='.$codepassword.'&email='.$email;
                $message = '<p>Elfelejetett jelszo</p>
                        <p>Kérjük kattintson a linkre, hogy uj jelszot generaljon: </p>
                        <a href="'.$verifyURL.'">'.$verifyURL.'</a>';
                if($query->execute()){
                    if(sendMail($email, "Elfelejtett jelszo", $message)){
                        json("A kerelmet sikeresen elkuldtuk, ellenorizze az email fiokjat", "ok");
                    }else{
                        json("Hiba tortent, probalja ujra");
                    }
                }else{
                    json("SQL hiba tortent");
                }
            }
        } else {
            json("Nincs ilyen felhasznalo " . $email);
        }
    } catch (PDOException $e) {
        json("SQL hiba tortent" . $e->getMessage());
    }
}
json("Empty request");