<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");

if(!isTrainer()) {
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

    try {
        $sql = "INSERT INTO exercises (ExerciseName, Description, TrainerID) VALUES (:en,:dc,:tid)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':en', $exerciseName);
        $query->bindParam(':dc', $description);
        $query->bindParam(':tid', $trainerID);
        $query->execute();
    } catch (PDOException $error) {
        die($error);
    }
}