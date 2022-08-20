<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");
require_once("User.php");
require_once("Trainer.php");

if (isPost() && !empty($_POST)) {
    addcategory();
} else {
    try {
        $_POST = json_decode(file_get_contents("php://input"), true);
        addcategory();
    } catch (Exception $e) {
        json("Not a POST request");
    }
}

function addcategory()
{
    global $dbh;
    $categoryName = sanitize($_POST["CategoryName"]);
    $categories = getCategories();
    if(strlen($categoryName) < 5){
        json("Túl rövid név");
    }
    if(strlen($categoryName) > 40){
        json("Túl hosszú név");
    }
    if(in_array($categoryName,array_column($categories, "CategoryName"))){
        json("Kategória már létezik");
    }
    try {
        $sql = "INSERT INTO categories (CategoryName) VALUES (:ctg)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':ctg', $categoryName);
        if ($query->execute()) {
            setAlert("Sikeres létrehozás","success");
            json("Sikeres létrehozás", "ok",["redirect" => "index.php?page=categories"]);
        } else {
            json("Sikertelen létrehozás");
        }
    } catch (PDOException $error) {
        die($error);
    }
}
json("Invalid post");