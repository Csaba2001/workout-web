<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");

if(!isTrainer()) {
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
    $exerciseID = sanitize($_POST["ExerciseID"]);
    $exerciseName = sanitize($_POST["ExerciseName"]);
    $description = sanitize($_POST["Description"]);
    $trainerID = $_SESSION['PersonID'];

    $action = sanitize($_POST["mod"]);
    if(!$action){
        json("No action");
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
                json("Sikeres modositas", "ok");
            } else {
                json("Sikertelen modositas");
            }
        }
        elseif($action === "Torol"){
            $sql = "DELETE FROM exercises WHERE ExerciseID = :eid AND TrainerID = :tid;";
            $query = $dbh->prepare($sql);
            $query->bindParam(":eid",$exerciseID);
            $query->bindParam(":tid",$trainerID);
            if($query->execute()){
                json("Sikeres torles","ok");
            }else{
                json("Sikertelen torles");
            }
        }else{
            json("Invalid action");
        }
    } catch (PDOException $error) {
        die($error);
    }
}
json("Invalid post");