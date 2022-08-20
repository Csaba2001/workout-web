<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");
require_once("User.php");

$user = new User();
$user = User::getCurrentUser();
if(!$user->isUser()){
    redirect("index.php?page=home");
}

global $dbh;

if(isPost() && !empty($_POST)){
    giveUpTraining();
}else{
    try {
        $_POST = json_decode(file_get_contents("php://input"), true);
        giveUpTraining();
    }catch(Exception $e){
        json("Not a POST request");
    }
}

function giveUpTraining(){
    global $dbh;
    $user = new User();
    $user = User::getCurrentUser();

    $trainingID = sanitize($_POST["TrainingID"]);
    $personID = $user->PersonID;

    try {
        if (!getTrainingsFromTrainingID($trainingID)) {
            json("Nincs ilyen edzésterv");
        }
        $sql = "DELETE FROM persons_trainings WHERE PersonID = :pid AND TrainingID = :tid;";
        $query = $dbh->prepare($sql);
        $query->bindParam(":pid", $personID);
        $query->bindParam(":tid", $trainingID);
        if($query->execute()){
            setAlert("Sikeresen leadta az edzéstervet","success");
            json("Sikeresen leadta az edzéstervet", "ok",["redirect" => "index.php?page=workout"]);
        }else{
            json("Sikertelen művelet");
        }
    }catch(PDOException $e){
        json("SQL hiba történt: ".$e->getMessage()); // vedd ki
    }
}