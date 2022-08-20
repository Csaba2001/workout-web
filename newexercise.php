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
        json("Rövid név");
    }
    if(strlen($description)<5){
        json("Rövid leírás");
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
    } catch (PDOException $error) {
        die($error);
    }
}