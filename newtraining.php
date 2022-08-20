<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");
require_once("User.php");
require_once("Trainer.php");

if(!User::getCurrentUser()) {
    redirect("index.php?page=home");
}
if (isPost() && !empty($_POST)) {
    addtraining();
} else {
    try {
        $_POST = json_decode(file_get_contents("php://input"), true);
        addtraining();
    } catch (Exception $e) {
        json("Not a POST request");
    }
}

function addtraining(){
    global $dbh;
    $user = new User();
    $user = User::getCurrentUser();
    if($user->isTrainer()){
        $trainerID = $user->PersonID;
    }else{
        $trainerID = 0;
    }
    $category = sanitize($_POST['trainingCategory']);
    $description = sanitize($_POST['description']);
    $mon = sanitize($_POST['Mon']);
    $tue = sanitize($_POST['Tue']);
    $wed = sanitize($_POST['Wed']);
    $thu = sanitize($_POST['Thu']);
    $fri = sanitize($_POST['Fri']);
    $sat = sanitize($_POST['Sat']);
    $sun = sanitize($_POST['Sun']);
    $days = [$mon,$tue,$wed,$thu,$fri,$sat,$sun];

    $categories = getCategories();

    if(!array_key_exists($category,$categories)){
        json("Nincs kategória kiválasztva");
    }

    if(strlen($description) < 5){
        json("Rövid a leírás");
    }


    try {
        $exercises = [];

        if($user->isTrainer()){
            $sql = "SELECT ExerciseID FROM exercises WHERE TrainerID IN (:ed, 0);";
            $query = $dbh->prepare($sql);
            $query->bindParam(':ed', $trainerID);
            $query->execute();
            $exercises = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        if($user->isUser()){
            $sql = "SELECT * FROM exercises;";
            $query = $dbh->prepare($sql);
            $query->execute();
            $exercises = $query->fetchAll(PDO::FETCH_ASSOC);
        }

        foreach($days as $day){
            if(!in_array($day,array_column($exercises, "ExerciseID"))){
                json("Nincs ilyen gyakorlatod.");
            }
        }

        $sql = "INSERT INTO trainings (TrainerID, CategoryID, description, Mon, Tue, Wed, Thu, Fri, Sat, Sun) VALUES (:tid,:ctg,:dsc,:mon,:tue,:wed,:thu,:fri,:sat,:sun)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':tid', $trainerID);
        $query->bindParam(':ctg', $category);
        $query->bindParam(':dsc', $description);
        $query->bindParam(':mon', $mon);
        $query->bindParam(':tue', $tue);
        $query->bindParam(':wed', $wed);
        $query->bindParam(':thu', $thu);
        $query->bindParam(':fri', $fri);
        $query->bindParam(':sat', $sat);
        $query->bindParam(':sun', $sun);
        if($query->execute()){
            if($user->isUser()){
                $trainingID = $dbh->lastInsertId();
                $sql = "INSERT INTO persons_trainings (PersonID, TrainingID) VALUES (:pid, :tid);";
                $query = $dbh->prepare($sql);
                $query->bindParam(':pid', $user->PersonID);
                $query->bindParam(':tid', $trainingID);
                $query->execute();
                setAlert("Sikeresen létrehozta az edzéstervet, automatikusan fel is lett véve","success");
                json("Sikeresen létrehozta az edzéstervet, automatikusan fel is lett véve","ok",["redirect" => "index.php?page=workout"]);
            }else {
                setAlert("Sikeresen létrehozta az edzéstervet","success");
                json("Sikeresen létrehozta az edzéstervet", "ok",["redirect" => "index.php?page=workout"]);
            }
        }else{
            json("Sikertelen művelet");
        }
    } catch (PDOException $e) {
        json("SQL hiba: ".$e->getMessage());
    }
}