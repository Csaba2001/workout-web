<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");

if(!isUser()){
    redirect("index.php?page=home");
}

global $dbh;

if(isPost() && !empty($_POST)){
    takeTraining();
}else{
    try {
        $_POST = json_decode(file_get_contents("php://input"), true);
        takeTraining();
    }catch(Exception $e){
        json("Not a POST request");
    }
}

function takeTraining(){
    global $dbh;
    $trainingID = sanitize($_POST["TrainingID"]);
    $personID = $_SESSION["PersonID"];

    try {
        if (!getTrainingsFromTrainingID($trainingID)) {
            json("Training doesn't exist");
        }
        $sql = "INSERT INTO persons_trainings (PersonID, TrainingID) VALUES (:pid, :tid);";
        $query = $dbh->prepare($sql);
        $query->bindParam(":pid", $personID);
        $query->bindParam(":tid", $trainingID);
        if($query->execute()){
            json("Sikeresen felvetted az edzestervet", "ok",["redirect" => "index.php?page=workout"]);
        }else{
            json("Sikertelen muvelet");
        }
    }catch(PDOException $e){
        json("SQL hiba tortent: ".$e->getMessage()); // vedd ki
    }
}