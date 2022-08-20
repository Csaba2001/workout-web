<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");
require_once("User.php");
require_once("Trainer.php");

if(!User::getCurrentUser()){
    redirect("index.php?page=home");
}
if(isPost() && !empty($_POST)){
    modtraining();
}else{
    try {
        $_POST = json_decode(file_get_contents("php://input"), true);
        modtraining();
    }catch(Exception $e){
        json("Not a POST request");
    }
}

function modtraining(){
    global $dbh;
    $user = new User();
    $user = User::getCurrentUser();

    $trainingID = sanitize($_POST["TrainingID"]);
    $category = sanitize($_POST["Category"]);
    $description = sanitize($_POST["Description"]);
    $mon = sanitize($_POST["Mon"]);
    $tue = sanitize($_POST["Tue"]);
    $wed = sanitize($_POST["Wed"]);
    $thu = sanitize($_POST["Thu"]);
    $fri = sanitize($_POST["Fri"]);
    $sat = sanitize($_POST["Sat"]);
    $sun = sanitize($_POST["Sun"]);
    $personID = $user->PersonID;

    $action = sanitize($_POST["mod"]);
    if(!$action){
        json("No action");
    }

    try {
        if($user->isTrainer()) {
            $sql = "SELECT * FROM trainings WHERE TrainingID = :tid;";
        }else{
            $sql = "SELECT trainings.* FROM trainings INNER JOIN persons_trainings ON persons_trainings.TrainingID = trainings.TrainingID WHERE trainings.TrainingID = :tid;";
        }
        $query = $dbh->prepare($sql);
        $query->bindParam(':tid', $trainingID);
        $query->execute();
        $results = $query->fetch(PDO::FETCH_ASSOC);
        if(!$results){
            json("Nincs ilyen edzésterv");
        }

        if ($action === "Modosit") {
            $sql = "UPDATE trainings SET CategoryID = :ctg, description = :dsc, Mon = :mon, Tue = :tue, Wed = :wed, Thu = :thu, Fri = :fri, Sat = :sat, Sun = :sun WHERE TrainingID = :tid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':tid', $trainingID);
            $query->bindParam(':ctg', $category);
            $query->bindParam(':dsc', $description);
            $query->bindParam(':mon', $mon);
            $query->bindParam(':tue', $tue);
            $query->bindParam(':wed', $wed);
            $query->bindParam(':thu', $thu);
            $query->bindParam(':fri', $fri);
            $query->bindParam(':sat', $sat);
            $query->bindParam(':sun', $sun);
            if ($query->execute()) {
                setAlert("Sikeres módosítás","success");
                json("Sikeres módosítás", "ok",["redirect" => "index.php?page=workout"]);
            } else {
                json("Sikertelen módosítás");
            }
        } elseif ($action === "Torol") {
            $sql = "DELETE FROM trainings WHERE TrainingID = :tid;";
            $query = $dbh->prepare($sql);
            $query->bindParam(":tid", $trainingID);
            if ($query->execute()) {
                setAlert("Sikeres törlés","success");
                json("Sikeres törlés", "ok",["redirect" => "index.php?page=workout"]);
            } else {
                json("Sikertelen törlés");
            }
        } else {
            json("Invalid action");
        }
    } catch (PDOException $e) {
        json("SQL hiba: ".$e->getMessage());
    }
}
json("Invalid post");