<?php
session_start();
include("functions.php");
include("User.php");
include("Trainer.php");

$user = new User();
$user = User::getCurrentUser();
if($user){
    $user->set(User::getCurrentUser());
    if($user->isTrainer()){
        $trainer = new Trainer();
        $trainer = $trainer::getFromID($user->PersonID);
    }
}

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <title>Workout</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <meta name="description" content="Személyes edző">
    <meta name="keywords" content="személyes edző edzés edz gym workout trainers trainer">
    <meta name="author" content="Dobó Csaba">
    <meta name="robots" content="noindex,nofollow">
    <meta name="googlebot" content="noindex,nofollow">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script type="application/javascript" src="scripts/shared.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<body class="bg-black bg-opacity-10">
<?php
if(isset($_SESSION["alert"])): ?>
<div class="toast-container p-3 position-fixed bottom-0 start-0">
    <div id="toastAlert" class="toast align-items-center text-white bg-<?= $_SESSION["alert"]["type"] ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <?= $_SESSION["alert"]["message"] ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<script>
    const toastLiveExample = $('toastAlert');
    const toast = new bootstrap.Toast(toastLiveExample);
    toast.show();
</script>

<?php clearAlert(); endif;

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
        case "users":
            include("pages/users.php");
            break;
        case "trainers":
            include("pages/trainers.php");
            break;
        case "categories":
            include("pages/categories.php");
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