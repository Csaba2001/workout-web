<?php
const SECRET = "eperfa28ha3";
require_once 'db_config.php';

session_start();
if(isset($_SESSION['Email'])){
    unset($_SESSION);
    session_destroy();
    header('Location: index.php');
}
function login($email,$password){
    if($email=='')
        return 'invalid username';
    if($password=='')
        return 'invalid password';
    try {
        $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER, DB_PASS,
            [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]);
    } catch (PDOException $e) {
        return "sql error";
    }

    $sql = "SELECT * FROM Persons WHERE Email=? Limit 1";
    $query = $dbh->prepare($sql);
    $query->bindParam(1,$email);
    try {
        $query->execute();
        $results = $query->fetch(PDO::FETCH_ASSOC);
        if ($results > 0) {
            if($results['PASSWORD']===$password){
                $_SESSION=$results;
                header('Location: index.php');
            }
            else{
                return 'invalid password';
            }
        } else {
            return 'invalid username';
        }
    }
    catch (PDOException $error){
        return 'error';
    }
}
if(isset($_POST['logusername']) && isset($_POST['logpassword'])){
    var_dump(login($_POST['logusername'],$_POST['logpassword']));
}