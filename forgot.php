<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");
require_once("User.php");

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

    $user = new User();
    $user->Email = $email;
    $user->Password = $newPassword;
    $user->PasswordConfirm = $newPasswordConfirm;
    $user->validateBasic();

    if($user->_errors){
        json(implode("<br>",$user->_errors));
    }

    $user = User::getFromEmail($email);
    if(!$user){
        json("Nincs ilyen felhasznalo");
    }
    $user->set($user);
    if($user->Verified === "pending"){
        json("A regisztracioja nincs visszaigazolva. Kerjuk elobb igazolja vissza.");
    }
    if($user->Status === "banned"){
        json("A felhasznalo fiokja le van tiltva. Nem kerhet uj jelszot.");
    }

    $now = new DateTime("now");
    $then = ($user->NewPasswordExpires) ? new DateTime($user->NewPasswordExpires) : null;

    if($user->NewPassword && $then){
        if($now > $then){
            $user->NewPassword = null;
            $user->CodePassword = null;
            $user->NewPasswordExpires = null;
            $user->save();
            json("Az elozo jelszo kerelem lejart, ezert toroltuk, generaljon ujat a Kuldes gombra kattintva.");
        }else{
            json("Mar kervenyezett elfelejtett jelszot az emult 24 oraban.");
        }
    }else{
        $passwordHash = password_hash($newPassword,PASSWORD_DEFAULT);
        $codepassword = bin2hex(random_bytes(10));
        $now->modify("+1 day");
        $newpasswordexpires = $now->format("Y-m-d H:i:s");
        $user->NewPassword = $passwordHash;
        $user->CodePassword = $codepassword;
        $user->NewPasswordExpires = $newpasswordexpires;

        $verifyURL = 'http://'.HOST.'/newpw.php?code='.$codepassword.'&email='.$email;
        $message = '<p>Elfelejetett jelszo</p>
                        <p>Kérjük kattintson a linkre, hogy uj jelszot generaljon: </p>
                        <a href="'.$verifyURL.'">'.$verifyURL.'</a>';
        if($user->save()){
            if(sendMail($email, "Elfelejtett jelszo", $message)){
                json("A kerelmet sikeresen elkuldtuk, ellenorizze az email fiokjat", "ok");
            }else{
                json("Hiba tortent, probalja ujra");
            }
        }else{
            json("SQL hiba tortent".$user->_errors["Error"]);
        }
    }
}
json("Empty request");