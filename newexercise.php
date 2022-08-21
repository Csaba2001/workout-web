<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");
require_once("User.php");
require_once("Trainer.php");

$user = new User();
$user = User::getCurrentUser();
if(!$user->isTrainer()) {
    redirect("index.php?page=home");
}
if (isPost() && !empty($_POST)) {
    addexercise();
} else {
    try {
        $_POST = json_decode(file_get_contents("php://input"), true);
        addexercise();
    } catch (Exception $e) {
        json("Not a POST request");
    }
}

function addexercise(){
    global $dbh;
    $exerciseName = sanitize($_POST["ExerciseName"]);
    $description = sanitize($_POST["Description"]);
    $trainerID = $_SESSION['PersonID'];

    if(strlen($exerciseName) < 4){
        json("Túl rövid a név");
    }
    if(strlen($exerciseName) > 30){
        json("Túl hosszú a név");
    }
    if(strlen($description) > 100){
        json("Túl hosszú a leírás");
    }

    try {
        $sql = "INSERT INTO exercises (ExerciseName, Description, TrainerID) VALUES (:en,:dc,:tid)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':en', $exerciseName);
        $query->bindParam(':dc', $description);
        $query->bindParam(':tid', $trainerID);
        if($query->execute()){
            json("Sikeres gyakorlat létrehozás","ok");
        }else{
            json("Hiba történt");
        }
    } catch (PDOException $e) {
        $existingkey = "Integrity constraint violation: 1062 Duplicate entry";
        if (strpos($e->getMessage(), $existingkey) !== FALSE) {
            json("Már létezik hasonló gyakorlat");
        }else {
            json("Hiba történt, próbálja újra");
        }
    }
}
json("Invalid request");