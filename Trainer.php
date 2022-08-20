<?php

class Trainer extends User {
    public $TrainerID, $CV, $rated = null, $rating = null, $approval;

    public static function getFromID($id){
        global $dbh;
        $sql = "SELECT * FROM trainers WHERE TrainerID = :tid;";
        $query = $dbh->prepare($sql);
        $query->bindParam(":tid", $id);
        $query->execute();
        $trainer = new Trainer();
        $trainer = $query->fetchObject("Trainer");

        $sql = "SELECT * FROM persons WHERE PersonID = :pid;";
        $query = $dbh->prepare($sql);
        $query->bindParam(":pid", $id);
        $query->execute();
        $user = new User();
        $user = $query->fetchObject("User");

        if($user && $trainer){
            return $trainer;
        }
        return false;
    }

    public static function getFromEmail($email){
        global $dbh;
        $sql = "SELECT trainers.* FROM trainers INNER JOIN persons ON trainers.TrainerID = persons.PersonID WHERE persons.Email = :email;";
        $query = $dbh->prepare($sql);
        $query->bindParam(":email", $email);
        $query->execute();
        $trainer = new Trainer();
        $trainer = $query->fetchObject("Trainer");
        if(!$trainer){
            return false;
        }
        return $trainer;
    }

    public function validateCV($cv){
        if(strlen($cv) < 20){
            $this->_errors["CV"] = "Túl rövid a CV";
            return false;
        }
        if(strlen($cv) > 2000){
            $this->_errors["CV"] = "Túl hosszú a CV";
            return false;
        }
        return true;
    }

    public function register(){
        $this->_errors = [];

        $trainer = new Trainer();
        $trainer = Trainer::getFromID($this->TrainerID);
        if($trainer){
            $this->_errors["Error"] = "Már létezik ez az edző";
            return false;
        }

        $user = new User();
        $user = User::getFromID($this->TrainerID);
        if(!$user){
            $this->_errors["Error"] = "Nincs ilyen felhasználó";
            return false;
        }
        if($user->Rank !== "trainer"){
            $this->_errors["Error"] = "Felhasználó nem edző";
            return false;
        }

        $this->validateCV($this->CV);

        if($this->_errors){
            return false;
        }

        if($this->save()){
            return true;
        }else{
            return false;
        }
    }

    public function isNew(){
        return empty($this->TrainerID);
    }

    public function save(){
        $this->validateCV($this->CV);
        if($this->_errors){
            return false;
        }

        global $dbh;
        if($this->isNew()){
            try {
                $message = '<p>A regisztracióját egy admin fogja elbirálni 24 órán belül.</p>
                             <p>Kérjük legyen türelemmel.</p>';
                $sql = "INSERT INTO trainers (TrainerID, CV, approval) VALUES (:trainerID, :cv, 'pending');";
                $query = $dbh->prepare($sql);
                $query->bindParam(":trainerID",$this->TrainerID);
                $query->bindParam(":cv", $this->CV);
                if($query->execute()){
                    if(sendMail($this->Email, "Regisztráció igazolás", $message)){
                        return true;
                    }else{
                        $this->_errors["Error"] = "Sikeretelen regisztráció, probálja újra";
                        return false;
                    }
                }
            }catch(PDOException $e){
                $this->_errors["Error"] = "SQL hiba tortent: ".$e->getMessage();
                return false;
            }
        }else{ // update trainer
            try {
                $sql = "UPDATE trainers SET CV = :cv, rated = :rated, rating = :rating, approval = :approval WHERE TrainerID = :trainerID;";

                $query = $dbh->prepare($sql);
                $query->bindParam(":trainerID", $this->TrainerID);
                $query->bindParam(":cv", $this->CV);
                $query->bindParam(":rated", $this->rated);
                $query->bindParam(":rating", $this->rating);
                $query->bindParam(":approval", $this->approval);

                if($query->execute()){
                    return true;
                }else{
                    return false;
                }
            }catch(PDOException $e){
                $this->_errors["Error"] = "SQL hiba történt: ".$e->getMessage();
                return false;
            }
        }
    }

}