<?php

include_once("functions.php");
/*
_/﹋\_
(҂`_´)
<,︻╦╤─ ҉ - -
_/﹋\_
   _____________
 _/_|[][][][][] | - -
(      Fortnite | - -
=--OO-------OO--=dwb
 */
class User {
    protected static $_currentUser;
    public static $userTypes = ["user", "trainer", "admin"];
    public $_errors = [];
    public
        $PersonID,
        $FirstName,
        $LastName,
        $Phone,
        $Email,
        $Password,
        $PasswordConfirm,
        $Hash,
        $VerifyCode = null,
        $RegistrationExpires = null,
        $NewPassword = null,
        $CodePassword = null,
        $NewPasswordExpires = null,
        $Verified,
        $Rank,
        $Status,
        $CV;

    public function login(){
        $this->_errors = [];

        $this->validateEmail($this->Email);
        $this->validatePassword($this->Password);

        $user = new User();
        $user = User::getFromEmail($this->Email);
        if($user){
            if(!password_verify($this->Password, $user->Hash)){
                $this->_errors["Hash"] = "Ervenytelen jelszo";
                return false;
            }
            if($user->Verified !== "verified"){
                $this->_errors["Error"] = "A felhasznalo nincs visszaigazolva kerjuk nezze meg az email fiokjat";
                return false;
            }
            if($user->Status === "blocked"){
                $this->_errors["Error"] = "A felhasznalo fiokja tiltva van";
                return false;
            }
            $this->set($user);
            $_SESSION["PersonID"] = $user->PersonID;
            return true;
        }else{
            $this->_errors["Email"] = "Nincs ilyen felhasznalo";
            return false;
        }
    }

    public function validateBasic(){
        $this->_errors = [];

        $this->validateEmail($this->Email);
        $this->validatePassword($this->Password);
        if($this->PasswordConfirm !== $this->Password){
            $this->_errors["Hash"] = "Jelszavak nem egyeznek";
            return false;
        }
        return true;
    }

    public function register(){
        $this->_errors = [];

        $this->validateFirstName($this->FirstName);
        $this->validateLastName($this->LastName);
        $this->validateEmail($this->Email);
        $this->validatePhone($this->Phone);
        $this->validatePassword($this->Password);
        if($this->PasswordConfirm !== $this->Password){
            $this->_errors["Hash"] = "Jelszavak nem egyeznek";
            return false;
        }
        if(!in_array($this->Rank, self::$userTypes)){
            $this->_errors["Rank"] = "Helytelen felhasznaloi jog";
            return false;
        }

        if($this->_errors){
            return false;
        }

        $this->Hash = password_hash($this->Password,PASSWORD_BCRYPT);
        $this->VerifyCode = bin2hex(random_bytes(10));
        $now = new DateTime("now");
        $now->modify("+1 day");
        $this->RegistrationExpires = $now->format("Y-m-d H:i:s");

        if($this->save()){
            return true;
        }else{
            return false;
        }
    }

    public function save(){
        $this->validateLastName($this->LastName);
        $this->validateFirstName($this->FirstName);
        $this->validateEmail($this->Email);
        $this->validatePhone($this->Phone);
        if($this->_errors){
            return false;
        }
        global $dbh;
        if($this->isNew()){
            try {
                $sql = "INSERT INTO persons (LastName, FirstName, Phone, Email, Hash, VerifyCode, RegistrationExpires, Rank) VALUES (:lastname,:firstname, :phone, :email, :hash, :verifycode, :registrationexpires, :rank);";
                $query = $dbh->prepare($sql);
                $query->bindParam(":lastname", $this->FirstName);
                $query->bindParam(":firstname", $this->LastName);
                $query->bindParam(":phone", $this->Phone);
                $query->bindParam(":email", $this->Email);
                $query->bindParam(":hash", $this->Hash);
                $query->bindParam(":verifycode", $this->VerifyCode);
                $query->bindParam(":registrationexpires", $this->RegistrationExpires);
                $query->bindParam(":rank", $this->Rank);

                if($query->execute()){
                    $lastID = $dbh->lastInsertId();
                    $verifyURL = 'http://'.HOST.'/verify.php?code='.$this->VerifyCode.'&email='.$this->Email;
                    $message = '<p>Köszönjük, hogy regisztrált</p>
                        <p>Kérjük kattintson a linkre, hogy a regisztrációja véglegesüljon: </p>
                        <a href="'.$verifyURL.'">'.$verifyURL.'</a>';
                    if($this->Rank === "user"){ //user
                        if(sendMail($this->Email, "Regisztráció igazolás", $message)){
                            return true;
                        }else{
                            $this->_errors["Error"] = "Sikeretelen regisztráció, probálja újra";
                            return false;
                        }
                    }else{ //trainer
                        $message .= '<p>A regisztracióját egy admin fogja elbirálni 24 órán belül.</p>
                             <p>Kérjük legyen türelemmel.</p>';
                        $sql = "INSERT INTO trainers (TrainerID, CV, approval) VALUES (:lastID, :cv, 'pending');";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(":lastID",$lastID);
                        $query->bindParam(":cv", $this->CV);
                        if($query->execute()){
                            if(sendMail($this->Email, "Regisztracio igazolas", $message)){
                                return true;
                            }else{
                                $this->_errors["Error"] = "Sikeretelen regisztráció, probálja újra";
                                return false;
                            }
                        }else{
                            $this->_errors["Error"] = "SQL hiba tortent: ";
                            return false;
                        }
                    }
                }else{
                    $this->_errors["Error"] = "SQL hiba tortent: ";
                    return false;
                }
            }catch(PDOException $e) {
                $existingkey = "Integrity constraint violation: 1062 Duplicate entry";
                if (strpos($e->getMessage(), $existingkey) !== FALSE) {
                    $this->_errors["Email"] = "Az email cim mar foglalt";
                    return false;
                }else {
                    $this->_errors["Error"] = "SQL hiba tortent: ".$e->getMessage();
                    return false;
                }
            }
        }else{ //modify user

            try {
                $sql="UPDATE persons SET LastName=:ln, FirstName=:fn, Phone=:ph, Email=:email, Hash=:hash, VerifyCode=:verifycode, RegistrationExpires=:regexp, NewPassword=:newpasswd, CodePassword = :codepassword, NewPasswordExpires = :newpasswdexp, Verified = :verified, Rank = :rank, Status = :status WHERE PersonID=:id;";

                $query=$dbh->prepare($sql);
                $query->bindParam(":id",$this->PersonID);
                $query->bindParam(":ln",$this->LastName);
                $query->bindParam(":fn",$this->FirstName);
                $query->bindParam(":ph",$this->Phone);
                $query->bindParam(":email",$this->Email);
                $query->bindParam(":hash",$this->Hash);
                $query->bindParam(":verifycode",$this->VerifyCode);
                $query->bindParam(":regexp",$this->RegistrationExpires);
                $query->bindParam(":newpasswd",$this->NewPassword);
                $query->bindParam(":codepassword",$this->CodePassword);
                $query->bindParam(":newpasswdexp",$this->NewPasswordExpires);
                $query->bindParam(":verified",$this->Verified);
                $query->bindParam(":rank",$this->Rank);
                $query->bindParam(":status",$this->Status);
                if($query->execute()){
                    return true;
                }
            }catch (PDOException $e){
                $this->_errors["Error"] = "SQL hiba tortent: ".$e->getMessage();
                return false;
            }
        }
    }

    public function isTrainer(){
        return $this->Rank === "trainer";
    }

    public function isUser(){
        return $this->Rank === "user";
    }

    public function isAdmin(){
        return $this->Rank === "admin";
    }

    public function isNew(){
        return empty($this->PersonID);
    }

    public static function getFromEmail($email){
        global $dbh;
        $sql = "SELECT * FROM persons WHERE Email = :email;";
        $query = $dbh->prepare($sql);
        $query->bindParam(":email", $email);
        $query->execute();
        $user = new User();
        $user = $query->fetchObject('User');
        if(!$user){
            return false;
        }
        return $user;
    }

    public static function getFromID($id){
        global $dbh;
        $sql = "SELECT * FROM persons WHERE PersonID = :pid;";
        $query = $dbh->prepare($sql);
        $query->bindParam(":pid", $id);
        $query->execute();
        $user = new User();
        $user = $query->fetchObject('User');
        if(!$user){
            return false;
        }
        return $user;
    }

    public function setFromEmail($email){
        $user = self::getFromEmail($email);
        $this->set($user);
    }
    public function setFromID($id){
        $user = self::getFromID($id);
        $this->set($user);
    }

    public function set(User $user){
        $this->PersonID = $user->PersonID;
        $this->FirstName = $user->FirstName;
        $this->LastName = $user->LastName;
        $this->Rank = $user->Rank;
        $this->Email = $user->Email;
        $this->Hash = $user->Hash;
        $this->Phone = $user->Phone;
        $this->VerifyCode = $user->VerifyCode;
        $this->RegistrationExpires = $user->RegistrationExpires;
        $this->CodePassword = $user->CodePassword;
        $this->NewPassword = $user->NewPassword;
        $this->NewPasswordExpires = $user->NewPasswordExpires;
        $this->Status = $user->Status;
        $this->Verified = $user->Verified;
        $this->CV = $user->CV;
    }

    public static function getCurrentUser(){
        if(!isset($_SESSION["PersonID"])){
            return false;
        }
        if(!self::$_currentUser && isset($_SESSION["PersonID"])){
            self::$_currentUser = self::getFromID($_SESSION["PersonID"]);
        }
        if(self::$_currentUser && self::$_currentUser->Status === "blocked"){
            self::$_currentUser->logout();
            setAlert("A felhasznaloi fiokja tiltva lett");
        }
        return self::$_currentUser;
    }

    public function setCurrentUser(){
        $this->set(self::getFromID($_SESSION["PersonID"]));
    }

    public function logout(){
        self::$_currentUser = false;
        unset($_SESSION);
        session_destroy();
    }

    public function validateEmail($email){
        if(!$email = filter_var($email,FILTER_VALIDATE_EMAIL)){
            $this->_errors["Email"] = "Helytelen email cim";
            return false;
        }
        return true;
    }

    public function validatePassword($password){
        if(!$password || strlen($password) < 3){
            $this->_errors["Hash"] = "Tul rovid a jelszo, minimum 3 karakter";
            return false;
        }
        if(strlen($password) > 12){
            $this->_errors["Hash"] = "Tul hosszu a jelszo, maximum 12 karakter";
            return false;
        }
        return true;
    }

    public function validateFirstName($firstname){
        if(!$firstname || strlen($firstname) < 3){
            $this->_errors["FirstName"] = "Tul rovid a utonev, minimum 3 karakter";
            return false;
        }
        if(strlen($firstname) > 30){
            $this->_errors["FirstName"] = "Tul hosszu a utonev, maximum 30 karakter";
            return false;
        }
        return true;
    }

    public function validateLastName($lastname){
        if(!$lastname || strlen($lastname) < 3){
            $this->_errors["FirstName"] = "Tul rovid a vezeteknev, minimum 3 karakter";
            return false;
        }
        if(strlen($lastname) > 30){
            $this->_errors["FirstName"] = "Tul hosszu a vezeteknev, maximum 30 karakter";
            return false;
        }
        return true;
    }

    public function validatePhone($phone){
        if(!$phone = preg_match('/^(\+?\d{2,3})?(0+?)?(\d{9})$/',$phone)){
            $this->_errors["Phone"] = "Helytelen telefonszam";
            return false;
        }
        return true;
    }

    public function displayName(){
        return $this->LastName." ".$this->FirstName;
    }

}