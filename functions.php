<?php
@define("SECRET", "eperfa28ha3");
require_once("db_config.php");

$days = [
    "Mon" => "Hétfő",
    "Tue" => "Kedd",
    "Wed" => "Szerda",
    "Thu" => "Csütörtök",
    "Fri" => "Péntek",
    "Sat" => "Szombat",
    "Sun" => "Vasárnap"
];

function setAlert($msg, $type = "danger"){
    if(isset($_SESSION["alert"])){
        unset($_SESSION["alert"]);
    }
    $_SESSION["alert"]["message"] = $msg;
    $_SESSION["alert"]["type"] = $type;
}
function clearAlert(){
    if(isset($_SESSION["alert"])){
        unset($_SESSION["alert"]);
    }
}
function redirect($URL){
    header("Location: $URL");
    echo "<script>window.location.href = '$URL';</script>";
    die();
}
function sanitize($str){
    $str = stripslashes($str);
    $str = trim($str);
    return $str = htmlspecialchars($str);
}
function json($str, $type = "error", $options = []){
    die(json_encode([
        "type" => $type,
        "message" => $str,
        "options" => $options
    ]));
}
function isPost(){
    return ($_SERVER["REQUEST_METHOD"] === "POST");
}
function isGet(){
    return ($_SERVER["REQUEST_METHOD"] === "GET");
}
function sendMail($to, $subject, $message){
    $headers = array(
        'From' => 'noreply@'.HOST,
        'X-Mailer' => 'PHP',
        'Content-Type' => 'text/html; charset=utf-8'
    );
    if(mail($to, $subject, $message, $headers)){
        return true;
    }else{
        return false;
    }
}

function getExercises(){
    global $dbh;
    $query = "";
    $results = "";
    $user = User::getCurrentUser();
    try {
        if($user->isTrainer()){
            $trainerID = $_SESSION["PersonID"];
            $sql = "SELECT * FROM exercises WHERE TrainerID IN (:ed, 0);";
            $query = $dbh->prepare($sql);
            $query->bindParam(':ed', $trainerID);
        }elseif($user->isUser() || $user->isAdmin()){
            $sql = "SELECT * FROM exercises;";
            $query = $dbh->prepare($sql);
        }
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
    }catch(Exception $e){
        throw new Exception("SQL error: ".$e->getMessage());
    }finally{
        return $results;
    }
}

function getCategories(){
    global $dbh;
    $query = "";
    $results = "";
    try {
        $sql = "SELECT * FROM categories;";
        $query = $dbh->prepare($sql);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
    }catch(Exception $e){
        throw new Exception("SQL error: ".$e->getMessage());
    }finally{
        return $results;
    }
}

function getTrainingsFromTrainingID($trainingID){
    global $dbh;
    $results = false;
    try {
        $sql = "SELECT * FROM trainings WHERE TrainingID = :tid;";
        $query = $dbh->prepare($sql);
        $query->bindParam(':tid', $trainingID);

        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);

    }catch(Exception $e){
        throw new Exception("SQL error: ".$e->getMessage());
    }finally{
        return $results;
    }
}

function getTrainers(){
    global $dbh;
    $results = false;
    try {
        $sql = "SELECT trainers.*, persons.* FROM trainers INNER JOIN persons ON trainers.TrainerID = persons.PersonID WHERE trainers.TrainerID <> 0;";
        $query = $dbh->prepare($sql);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
    }catch(Exception $e){
        throw new Exception("SQL error: ".$e->getMessage());
    }finally{
        return $results;
    }
}

function getPersons(){
    global $dbh;
    $results = false;
    try{
        $sql="SELECT * FROM persons WHERE PersonID <> 0;";
        $query=$dbh->prepare($sql);
        $query->execute();
        $results=$query->fetchAll(PDO::FETCH_ASSOC);
    }catch (Exception $e){
        throw new Exception("SQL error: ".$e->getMessage());
    } finally {
        return $results;
    }
}
