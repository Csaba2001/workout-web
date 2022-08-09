<?php
@session_start();
require_once("functions.php");
require_once("db_config.php");

if(!isTrainer()){
    redirect("index.php?page=home");
    die();
}

global $dbh;
try {
    $personId=$_SESSION['PersonID'];                                          //keresés yo skrill drop it hard
    $sql = "SELECT ExerciseName, Description FROM exercises WHERE TrainerID=:ed;";
    $query = $dbh->prepare($sql);
    $query->bindParam(':ed', $personId);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $error) {
    die($error);
}
print_r($results);
?>

<div class="container col-lg-4">
    <h2>Uj gyakorlat letrehozasa</h2>
    <form method="post" action="newexercise.php" enctype="application/x-www-form-urlencoded">
        <div class="mb-3">
            <label for="ExerciseName" class="form-label">Gyakorlat</label>
            <input type="text" class="form-control" id="ExerciseName" name="ExerciseName">
        </div>

        <div class="mb-3">
            <label for="Description" class="form-label">Gyakorlat leirasa</label>
            <textarea id="Description" class="form-control" name="Description"></textarea>
        </div>

        <div class="alert alert-danger mt-2" role="alert" style="display: none;"></div>

        <input type="reset" class="btn btn-danger" value="Torles">
        <input type="submit" class="btn btn-primary" value="Letrehozas">
    </form>
</div>

<script src="scripts/forms.js"></script>