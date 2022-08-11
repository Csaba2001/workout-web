<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");

if(!isLoggedIn()) {
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
    $trainerID = $_SESSION['Rank']==="trainer"?$_SESSION['PersonID']:0;
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

    $categories = [
        "weightloss",
        "cutting",
        "bulking"
    ];
    if(!in_array($category,$categories)){
        json("Nincs kategoria kivalasztva");
    }

    if(strlen($description) < 5){
        json("Rovid a leiras");
    }


    try {
        $sql = "SELECT Rank FROM persons WHERE PersonID = :pid;";
        $query = $dbh->prepare($sql);
        $query->bindParam(':pid', $trainerID);
        $query->execute();
        $person = $query->fetch(PDO::FETCH_ASSOC);
        $exercises = [];

        if($person["Rank"] === "trainer"){
            $sql = "SELECT ExerciseID FROM exercises WHERE TrainerID IN (:ed, 0);";
            $query = $dbh->prepare($sql);
            $query->bindParam(':ed', $trainerID);
            $query->execute();
            $exercises = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        if($person["Rank"] === "user"){
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


        $sql = "INSERT INTO trainings (TrainerID, Category, description, Mon, Tue, Wed, Thu, Fri, Sat, Sun) VALUES (:tid,:ctg,:dsc,:mon,:tue,:wed,:thu,:fri,:sat,:sun)";
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
            json("Sikeres edezterv letrehozas","ok");
        }else{
            json("Sikertelen muvelet");
        }
    } catch (PDOException $error) {
        die($error);
    }
}