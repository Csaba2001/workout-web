<?php
@session_start();
require_once("functions.php");
require_once("db_config.php");

if(!isLoggedIn()){
    redirect("index.php?page=home");
}
global $dbh;

//$asd=$_SESSION['PersonID'];                                          //keresés
/*$sql = "SELECT trainings.Category, persons.FirstName, trainers.rating, t1.ExerciseName, t2.ExerciseName, t3.ExerciseName, t4.ExerciseName, t5.ExerciseName, t6.ExerciseName, t7.ExerciseName, trainings.TrainingID, persons.PersonID
FROM trainings 
LEFT JOIN exercises t1 ON trainings.Mon=t1.ExerciseID
LEFT JOIN exercises t2 ON trainings.Tue=t2.ExerciseID
LEFT JOIN exercises t3 ON trainings.Wed=t3.ExerciseID
LEFT JOIN exercises t4 ON trainings.Thu=t4.ExerciseID
LEFT JOIN exercises t5 ON trainings.Fri=t5.ExerciseID
LEFT JOIN exercises t6 ON trainings.Sat=t6.ExerciseID
LEFT JOIN exercises t7 ON trainings.Sun=t7.ExerciseID
INNER JOIN persons ON trainings.TrainerID = persons.PersonID
INNER JOIN trainers ON trainings.TrainerID = trainers.TrainerID
WHERE persons.PersonID=:ed";
$query = $dbh->prepare($sql);
$query->bindParam(':ed', $asd, PDO::PARAM_STR);*/
if(isTrainer()) {
    try {
        $personId=$_SESSION['PersonID'];
        $sql="";
        $sql = "SELECT * FROM exercises WHERE TrainerID IN (:ed, 0);";
        $query = $dbh->prepare($sql);
        $query->bindParam(':ed', $personId);
        $query->execute();
        $exercises = $query->fetchAll(PDO::FETCH_ASSOC);

        $sql = "SELECT trainings.Category, persons.FirstName, trainers.rating, trainings.description as Description, t1.ExerciseName as Mon, t2.ExerciseName as Tue, t3.ExerciseName as Wed, t4.ExerciseName as Thu, t5.ExerciseName as Fri, t6.ExerciseName as Sat, t7.ExerciseName as Sun, trainings.TrainingID, persons.PersonID
        FROM trainings 
        LEFT JOIN exercises t1 ON trainings.Mon=t1.ExerciseID
        LEFT JOIN exercises t2 ON trainings.Tue=t2.ExerciseID
        LEFT JOIN exercises t3 ON trainings.Wed=t3.ExerciseID
        LEFT JOIN exercises t4 ON trainings.Thu=t4.ExerciseID
        LEFT JOIN exercises t5 ON trainings.Fri=t5.ExerciseID
        LEFT JOIN exercises t6 ON trainings.Sat=t6.ExerciseID
        LEFT JOIN exercises t7 ON trainings.Sun=t7.ExerciseID
        INNER JOIN persons ON trainings.TrainerID = persons.PersonID
        INNER JOIN trainers ON trainings.TrainerID = trainers.TrainerID
        WHERE persons.PersonID=:ed ORDER BY trainings.TrainingID DESC";
        $query = $dbh->prepare($sql);
        $query->bindParam(':ed', $personId);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $error) {
        die($error);
    }
}
if(isUser()){
    try {
        $personId=$_SESSION['PersonID'];
        $sql="";
        $sql= "SELECT * FROM exercises";
        $query = $dbh->prepare($sql);
        $query->execute();
        $exercises = $query->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $error) {
        die($error);
    }
}

$days = ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"];
?>


<div class="searchbar" style="width: 100%; max-width: 100%;">
    <?php if(!empty($exercises)): ?>
    <form id="newTrainingForm" name="newTrainingForm" action="newtraining.php" method="post" enctype="application/x-www-form-urlencoded">
        <table class="table table-sm table-dark table-striped" style="text-align: center;">
            <thead>
                <tr>
                    <th scope="col">Kategória</th>
                    <th scope="col">Edzés leírása</th>
                    <th scope="col">Hétfő</th>
                    <th scope="col">Kedd</th>
                    <th scope="col">Szerda</th>
                    <th scope="col">Csütörtök</th>
                    <th scope="col">Péntek</th>
                    <th scope="col">Szombat</th>
                    <th scope="col">Vasárnap</th>
                    <th scope="col">Hozzáadás</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select id="trainingCategory" name="trainingCategory" class="form-select" aria-label=".form-select-lg">
                            <option value="nocateg">Kategória</option>
                            <option value="weightloss">Fogyás</option>
                            <option value="cutting">Szálkásítás</option>
                            <option value="bulking">Erősítés</option>
                        </select>
                    </td>
                    <td>
                        <textarea id="description" name="description"></textarea>
                    </td>
                    <?php for($i = 0; $i < 7; $i++): ?>
                    <td>
                        <select id="<?= $days[$i] ?>" name="<?= $days[$i] ?>">
                            <?php foreach($exercises as $exercise) : ?>
                            <option value="<?= $exercise["ExerciseID"] ?>"><?= $exercise["ExerciseName"] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <?php endfor; ?>
                    <td>
                        <input type="submit" class="btn btn-danger" value="Letrehoz">
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="alert alert-danger mt-2" role="alert" style="display: none;"></div>
    </form>
    <?php else : ?>
    <span>Nincs egy gyakorlata sem, keszitsen parat...</span>
    <a href="index.php?page=execa" class="btn btn-primary">Uj gyakorlat</a>
    <?php endif; ?>
</div>

<table class="table table-small table-dark table-striped" style=" text-align: center;" >
    <tr>
        <th scope="col">Kategória</th>
        <th scope="col">Edzés leírása</th>
        <th scope="col">Hétfő</th>
        <th scope="col">Kedd</th>
        <th scope="col">Szerda</th>
        <th scope="col">Csütörtök</th>
        <th scope="col">Péntek</th>
        <th scope="col">Szombat</th>
        <th scope="col">Vasárnap</th>
        <th scope="col">Értékelés</th>
        <th scope="col">&nbsp;Törlés</th>
    </tr>
    <?php
        if(isset($results)) :
            foreach ($results as $result) : ?>
                <tr>
                    <td><?= $result["Category"] ?></td>
                    <td><?= $result["Description"] ?></td>
                    <td><?= $result["Mon"] ?></td>
                    <td><?= $result["Tue"] ?></td>
                    <td><?= $result["Wed"] ?></td>
                    <td><?= $result["Thu"] ?></td>
                    <td><?= $result["Fri"] ?></td>
                    <td><?= $result["Sat"] ?></td>
                    <td><?= $result["Sun"] ?></td>
                    <td><?= $result["rating"] ?></td>
                    <td><form><input type="hidden" name="TrainingID" id="TrainingID" value="<?= $result["TrainingID"] ?>"><input type="submit" class="btn btn-danger" value="Torles"></form></td>
                </tr>
            <?php endforeach; else : ?>
            <tr>
                <td colspan=\"8\">Nincs egy gyakorlata sem, keszitsen parat...</td>
            </tr>
        <?php endif; ?>
</table>
<?php //endif; ?>
<script src="scripts/forms.js"></script>
