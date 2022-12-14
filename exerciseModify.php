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
    modexercise();
} else {
    try {
        $_POST = json_decode(file_get_contents("php://input"), true);
        modexercise();
    } catch (Exception $e) {
        json("Not a POST request");
    }
}

function modexercise(){
    global $dbh;
    $user = new User();
    $user = User::getCurrentUser();

    $exerciseID = sanitize($_POST["ExerciseID"]);
    $exerciseName = sanitize($_POST["ExerciseName"]);
    $description = sanitize($_POST["Description"]);
    $trainerID = $user->PersonID;

    $action = sanitize($_POST["mod"]);
    if(!$action){
        json("No action");
    }
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
        $sql = "SELECT * FROM exercises WHERE ExerciseID = :eid AND TrainerID = :tid;";
        $query = $dbh->prepare($sql);
        $query->bindParam(':eid', $exerciseID);
        $query->bindParam(':tid', $trainerID);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        if(!$results){
            json("Invalid exercise");
        }

        if($action === "Modosit") {
            $sql = "UPDATE exercises SET ExerciseName = :en, Description = :dc WHERE ExerciseID = :eid;";
            $query = $dbh->prepare($sql);
            $query->bindParam(':en', $exerciseName);
            $query->bindParam(':dc', $description);
            $query->bindParam(':eid', $exerciseID);
            if ($query->execute()) {
                json("Sikeres módosítás", "ok");
            } else {
                json("Sikertelen módosítás");
            }
        }
        elseif($action === "Torol"){
            $sql = "DELETE FROM exercises WHERE ExerciseID = :eid AND TrainerID = :tid;";
            $query = $dbh->prepare($sql);
            $query->bindParam(":eid",$exerciseID);
            $query->bindParam(":tid",$trainerID);
            if($query->execute()){
                setAlert("Sikeres törlés","success");
                json("Sikeres törlés","ok",["redirect" => "index.php?page=execa"]);
            }else{
                json("Sikertelen törlés");
            }
        }else{
            json("Invalid action");
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
json("Invalid post");