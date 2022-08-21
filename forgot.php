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

    $email = sanitize($_POST["Email"]);
    $newPassword = sanitize($_POST["Hash"]);
    $newPasswordConfirm = sanitize($_POST["PasswordConfirm"]);

    $user = new User();
    $user->Email = $email;
    $user->Password = $newPassword;
    $user->PasswordConfirm = $newPasswordConfirm;
    $user->validateBasic();

    if($user->_errors){
        json("Sikertelen művelet","error",["errors" => $user->_errors]);
    }

    $user = User::getFromEmail($email);
    if(!$user){
        json("Nincs ilyen felhasználó");
    }
    $user->set($user);
    if($user->Verified === "pending"){
        json("A regisztrációja nincs visszaigazolva. Kérjük előbb igazolja vissza");
    }
    if($user->Status === "banned"){
        json("A felhasználó fiókja le van tiltva. Nem kérhet új jelszót");
    }

    $now = new DateTime("now");
    $then = ($user->NewPasswordExpires) ? new DateTime($user->NewPasswordExpires) : null;

    if($user->NewPassword && $then){
        if($now > $then){
            $user->NewPassword = null;
            $user->CodePassword = null;
            $user->NewPasswordExpires = null;
            $user->save();
            json("Az előző jelszó kérelem lejárt, ezért töröltük, generáljon újat az űrlap elküldésével");
        }else{
            json("Már kérvényezett elfelejtett jelszót az emúlt 24 órában");
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
            if(sendMail($email, "Elfelejtett jelszó", $message)){
                setAlert("A kérelmet sikeresen elküldtük, ellenőrizze az email fiókját","success");
                json("A kérelmet sikeresen elküldtük, ellenőrizze az email fiókját", "ok",["redirect" => "index.php?page=home"]);
            }else{
                json("Hiba történt, próbálja újra");
            }
        }else{
            json("Hiba történt, próbálja újra");
        }
    }
}
json("Empty request");