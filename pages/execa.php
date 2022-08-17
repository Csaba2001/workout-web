<?php
@session_start();
require_once("functions.php");
require_once("db_config.php");
require_once("User.php");
require_once("Trainer.php");

if(!$user->isTrainer()){
    redirect("index.php?page=home");
}

global $dbh;
global $categories;
try {
    $personId=$_SESSION['PersonID'];
    $sql = "SELECT * FROM exercises WHERE TrainerID=:ed;";
    $query = $dbh->prepare($sql);
    $query->bindParam(':ed', $personId);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $error) {
    die($error);
}

?>

<div class="container col-lg-4">
    <h2 class="mt-3">Uj gyakorlat letrehozasa</h2>
    <form ajax method="post" action="newexercise.php" enctype="application/x-www-form-urlencoded">
        <div class="mb-3">
            <label for="ExerciseName" class="form-label">Gyakorlat</label>
            <input type="text" class="form-control" id="ExerciseName" name="ExerciseName">
        </div>

        <div class="mb-3">
            <label for="Description" class="form-label">Gyakorlat leirasa</label>
            <textarea id="Description" class="form-control" name="Description"></textarea>
        </div>

        <div class="alert alert-danger mt-2" role="alert" style="display: none;"></div>

        <input type="submit" class="btn btn-primary" value="Letrehozas">
        <input class="btn btn-secondary" type="reset" value="Megsem">
    </form>
</div>
<div class="container">
    <?php foreach($results as $result) : ?>
    <form ajax action="exerciseModify.php" method="post" enctype="application/x-www-form-urlencoded" id="exercise<?= $result["ExerciseID"] ?>Form" name="exercise<?= $result["ExerciseID"] ?>Form" >
        <input type="hidden" name="ExerciseID" id="ExerciseID" value="<?= $result["ExerciseID"] ?>">
        <label for="ExerciseName" class="form-label">Gyakorlat</label>
        <input type="text" class="form-control" id="ExerciseName" name="ExerciseName" value="<?= $result["ExerciseName"] ?>">

        <label for="Description" class="form-label">Gyakorlat leirasa</label>
        <textarea id="Description" class="form-control" name="Description"><?= $result["Description"] ?></textarea>

        <div class="alert alert-danger mt-2" role="alert" style="display: none;"></div>
        <input class="btn btn-primary" type="submit" mod="Modosit" value="Modosit">
        <input class="btn btn-danger" type="submit" mod="Torol" value="Torol">
    </form>
    <?php endforeach; ?>
</div>

<script src="scripts/forms.js"></script>