<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");

if(!isLoggedIn()){
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

function modtraining()
{
    global $dbh;
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
    $personID = $_SESSION['PersonID'];

    $action = sanitize($_POST["mod"]);
    if(!$action){
        json("No action");
    }

    try {
        if(isTrainer()) {
            $sql = "SELECT * FROM trainings WHERE TrainingID = :tid;";
        }else{
            $sql = "SELECT trainings.* FROM trainings INNER JOIN persons_trainings ON persons_trainings.TrainingID = trainings.TrainingID WHERE trainings.TrainingID = :tid;";
        }
        $query = $dbh->prepare($sql);
        $query->bindParam(':tid', $trainingID);
        $query->execute();
        $results = $query->fetch(PDO::FETCH_ASSOC);
        if(!$results){
            json("Invalid exercise");
        }
        if($results["status"] === "banned"){
            json("Tiltott edzesterv");
        }

        if ($action === "Modosit") {
            $sql = "UPDATE trainings SET Category = :ctg, description = :dsc, Mon = :mon, Tue = :tue, Wed = :wed, Thu = :thu, Fri = :fri, Sat = :sat, Sun = :sun WHERE TrainingID = :tid";
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
            //$query->bindParam(':pid', $personID);
            if ($query->execute()) {
                json("Sikeres modositas", "ok",["redirect" => "index.php?page=workout"]);
            } else {
                json("Sikertelen modositas");
            }
        } elseif ($action === "Torol") {
            $sql = "DELETE FROM trainings WHERE TrainingID = :tid;";
            $query = $dbh->prepare($sql);
            $query->bindParam(":tid", $trainingID);
            if ($query->execute()) {
                json("Sikeres torles", "ok",["redirect" => "index.php?page=workout"]);
            } else {
                json("Sikertelen torles");
            }
        } else {
            json("Invalid action");
        }
    } catch (PDOException $error) {
        die($error);
    }
}

json("Invalid post");