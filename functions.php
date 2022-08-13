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

$categories = [
    "weightloss" => "Fogyás",
    "cutting" => "Szálkásítás",
    "bulking" => "Erősítés"
];


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
function isTrainer(){
    return !empty($_SESSION) && $_SESSION["Rank"] === 'trainer';
}
function isUser(){
    return !empty($_SESSION) && $_SESSION["Rank"] === 'user';
}
function isAdmin(){
    return !empty($_SESSION) && $_SESSION["Rank"] === 'admin';
}
function isLoggedIn(){
    return isTrainer() or isUser() or isAdmin();
}
function logout(){
    unset($_SESSION);
    session_destroy();
    redirect("index.php?page=home");
}
function getExercises(){
    global $dbh;
    $query = "";
    $results = "";
    try {
        if(isTrainer()){
            $trainerID = $_SESSION["PersonID"];
            $sql = "SELECT * FROM exercises WHERE TrainerID IN (:ed, 0);";
            $query = $dbh->prepare($sql);
            $query->bindParam(':ed', $trainerID);
        }elseif(isUser()){
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
