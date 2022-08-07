<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script>
        var myWindow;
        function openWin() {
            myWindow = window.open("#divOne", "myWindow", "width=200,height=100");
        }

    </script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu" aria-controls="mainMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainMenu">
            <a class="navbar-brand" href="index.php?page=home">Személyi edző</a>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=home">Kezdőlap</a>
                </li>

                <?php if(isset($_SESSION['Email'])): ?><!-- user, trainer, admin -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=workout">Edzéstervek</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Kijelentkezés</a>
                </li>
                <?php if($_SESSION['Rank'] == "trainer") : ?><!-- for trainers,    other auths: user, trainer, admin -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=execa">Edzés hozzáadása</a>
                </li>
                <?php elseif($_SESSION['Rank'] == "admin") : ?><!-- admin -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=execa">Edzés hozzáadása</a>
                </li>
                <?php endif; else : ?><!-- guest -->
                <li class="nav-item" data-bs-toggle="modal" data-bs-target="#loginModal">
                    <a class="nav-link" role="button">Bejelentkezés</a>
                </li>
                <li class="nav-item" data-bs-toggle="modal" data-bs-target="#registerModal">
                    <a class="nav-link" role="button">Regisztráció</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<?php
if(isset($_GET['page'])){
    $page=$_GET['page'];
}
else{
    header('location: index.php?page=home');
}
switch ($page){
    case 'workout':
        if(isset($_SESSION['Email'])){
            include "workout.php";
            //var_dump($_SESSION);
        }
        break;
    case 'execa':
        if(isset($_SESSION['Email'])&&$_SESSION['Rank']=='trainer'){
            include "execa.php";
        }
        break;
    case 'logout':
        header('location: login.php');
        break;
    case 'home':
        include "home.php";
        break;
    default:
        echo 'Nincs ilyen oldal';
        break;
}
?>
<div class="overlay" id="divOne">
    <div class="wrapper">
        <h2>Bejelentkezés</h2>
        <a href="#" class="close">&times;</a>
        <div class="content">
            <div class="container">
                <form action="login.php" method="post" enctype="application/x-www-form-urlencoded">
                    <label for="logusername">Felhasználónév</label>
                    <input type="text" placeholder="Felhasználónév" name="logusername" id="logusername">
                    <label for="password">Jelszó</label>
                    <input type="text" placeholder="Jelszó" name="logpassword" id="logpassword">
                    <button type="submit">Bejelentkezés</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="overlay" id="divTwo">
    <div class="wrapper">
        <h2>Regisztráció</h2>
        <a href="#" class="close">&times;</a>
        <div class="content">
            <div class="container">
                <form>
                    <label for="email">E-mail-cím</label>
                    <input type="text" placeholder="E-mail-cím" name="email" id="email">
                    <label for="username">Felhasználónév</label>
                    <input type="text" placeholder="Felhasználónév" name="username" id="username">
                    <label for="password">Jelszó</label>
                    <input type="text" placeholder="Jelszó" name="password" id="password">
                    <label for="password">Jelszó megerőítése</label>
                    <input type="text" placeholder="Jelszó megerőítése" name="passwordrp" id="passwordrp">
                    <input type="checkbox" id="trainer" name="trainer" value="trainer">
                    <label for="trainer"> Regisztrálás mint edző</label><br>
                    <button type="submit">Regisztráció</button>
                </form>
            </div>
        </div>
    </div>
</div-->
<?php endif; ?>