<?php
@session_start();
require_once("functions.php");
require_once("db_config.php");
require_once("User.php");
require_once("Trainer.php");

if (isPost() && !empty($_POST)) {
    modcategory();
} else {
    try {
        $_POST = json_decode(file_get_contents("php://input"), true);
        modcategory();
    } catch (Exception $e) {
        json("Not a POST request");
    }
}

function modcategory(){
    global $dbh;
    $categoryID = sanitize($_POST["CategoryID"]);
    $categoryName = sanitize($_POST["CategoryName"]);
    $action=sanitize($_POST["mod"]);
    if(!$action){
        json('No action');
    }
    if(strlen($categoryName) < 5){
        json("Túl rövid név");
    }
    if(strlen($categoryName) > 40){
        json("Túl hosszú név");
    }
    try {
        if($action=="Modosit"){
            $sql= "UPDATE categories SET CategoryName=:ctg WHERE CategoryID=:cid";
            $query=$dbh->prepare($sql);
            $query->bindParam(':ctg',$categoryName);
            $query->bindParam('cid', $categoryID);
            if($query->execute()){
                json("Sikeres módosítás","ok");
            }else{
                json("Sikertelen módosítás");
            }
        }
        elseif($action=="Torol"){
            $sql="DELETE FROM categories WHERE CategoryID=:cid";
            $query=$dbh->prepare($sql);
            $query->bindParam(":cid",$categoryID);
            if($query->execute()){
                json("Sikeres törlés", "ok",["redirect" => "index.php?page=categories"]);
            }else{
                json("Sikertelen törlés");
            }
        }
        json("Hibás művelet");
    }catch (PDOException $error){
        die($error);
    }
}
json("Invalid post");

