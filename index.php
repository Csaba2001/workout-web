<?php
session_start();
include("functions.php");


?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
    <!--link rel="stylesheet" type="text/css" href="style.css"-->
    <script type="application/javascript" src="scripts/shared.js"></script>
</head>
<body>
    <?php
    include("navbar.php");

    if(!isset($_GET["page"])){
        redirect("index.php?page=home");
    }else{
        $page = sanitize($_GET["page"]);
        switch($page){
            case "execa":
                include("pages/execa.php");
                break;
            case "search":
                include("pages/search.php");
                break;
            case "profile":
                include("pages/profile.php");
                break;
            case "workout":
                include("pages/workout.php");
                break;
            case "home":
            default:
                include("pages/home.php");
                break;
        }
    }
    ?>
</body>
</html>