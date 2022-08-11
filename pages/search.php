<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");

if(!isUser()){
    redirect("index.php?page=home");
}
global $dbh;
$trainers = [];
$results = [];

try {
    $sql = "SELECT DISTINCT(trainings.TrainerID), trainers.rating, trainers.rated, persons.FirstName, persons.LastName FROM trainings INNER JOIN persons ON trainings.TrainerID = persons.PersonID INNER JOIN trainers ON trainings.TrainerID = trainers.TrainerID;";
    $query = $dbh->prepare($sql);
    $query->execute();
    $trainers = $query->fetchAll(PDO::FETCH_ASSOC);
}catch(PDOException $e){
    $error = "Hiba tortent".$e->getMessage(); //vedd ki vegen
}

if(isPost()){
    $category = sanitize($_POST['category']);
    $trainer = sanitize($_POST['trainer']);

    $categories = [
        "weightloss",
        "cutting",
        "bulking"
    ];
    if(!in_array($category,$categories)){
        $category = null;
    }

    if(!in_array($trainer, $trainers)){
        $trainer = null;
    }

    try {
        $sql = "SELECT trainings.Category, trainings.description as Description, persons.FirstName, trainers.rating, t1.ExerciseName as Mon, t2.ExerciseName as Tue, t3.ExerciseName as Wed, t4.ExerciseName as Thu, t5.ExerciseName as Fri, t6.ExerciseName as Sat, t7.ExerciseName as Sun, trainings.TrainingID
        FROM trainings 
        LEFT JOIN exercises t1 ON trainings.Mon=t1.ExerciseID
        LEFT JOIN exercises t2 ON trainings.Tue=t2.ExerciseID
        LEFT JOIN exercises t3 ON trainings.Wed=t3.ExerciseID
        LEFT JOIN exercises t4 ON trainings.Thu=t4.ExerciseID
        LEFT JOIN exercises t5 ON trainings.Fri=t5.ExerciseID
        LEFT JOIN exercises t6 ON trainings.Sat=t6.ExerciseID
        LEFT JOIN exercises t7 ON trainings.Sun=t7.ExerciseID
        INNER JOIN persons ON trainings.TrainerID = persons.PersonID
        INNER JOIN trainers ON trainings.TrainerID = trainers.TrainerID";
        if($category){
            $sql .= " WHERE trainings.Category=:category";

        }
        if($trainer){
            $sql .= " WHERE trainings.TrainerID = :trainerID";
        }

        $query = $dbh->prepare($sql);

        if($category){
            $query->bindParam(":category",$category);
        }
        if($trainer){
            $query->bindParam(":trainerID",$trainer);
        }
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        if(empty($results)){
            $error = "Nincs talalat";
        }
    }catch(PDOException $e){
        $error = "Hiba tortent".$e->getMessage(); //vedd ki vegen
    }
}
?>
<div class="container d-flex flex-row justify-content-center">
    <form class="m-3 d-flex flex-row" method="post" action="index.php?page=search" enctype="application/x-www-form-urlencoded">
        <select id="category" name="category" class="form-select m-2">
            <option>Kategória</option>
            <option value="weightloss">Fogyás</option>
            <option value="cutting">Szálkásítás</option>
            <option value="bulking">Erősítés</option>
        </select>
    <?php if(isset($trainers) && !empty($trainers)) : ?>
        <select id="trainer" name="trainer" class="form-select m-2">
            <option>Edző</option>
            <?php foreach($trainers as $trainer) : ?>
                <option value="<?= $trainer["TrainerID"] ?>"><?= $trainer["FirstName"] ?> <?= $trainer["LastName"] ?> <?= $trainer["rating"] ?>/<?= $trainer["rated"] ?></option>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>
            <input type="submit" class="btn btn-secondary m-2" value="Keresés"><br/>
    </form>
</div>
<?php if(isset($error) && !empty($error)) : ?>
    <div class="alert alert-danger mt-2" role="alert"><?= $error ?></div>
<?php endif; ?>

<?php if(!empty($results)) : ?>
<div class="searchbar">
    <form method="post">
        <table class="table table-dark table-striped table-hover" style="text-align: center">
            <tr>
                <th scope="col">Kategória</th>
                <th scope="col">Edző</th>
                <th scope="col">Edzés leírása</th>
                <th scope="col">Hétfő</th>
                <th scope="col">Kedd</th>
                <th scope="col">Szerda</th>
                <th scope="col">Csütörtök</th>
                <th scope="col">Péntek</th>
                <th scope="col">Szombat</th>
                <th scope="col">Vasárnap</th>
                <th scope="col">Értékelés</th>
            </tr>
            <?php foreach ($results as $result) : ?>
            <tr>
                <td><?= $result["Category"] ?></td>
                <td><?= $result["FirstName"] ?></td>
                <td><?= $result["Description"] ?></td>
                <td><?= $result["Mon"] ?></td>
                <td><?= $result["Tue"] ?></td>
                <td><?= $result["Wed"] ?></td>
                <td><?= $result["Thu"] ?></td>
                <td><?= $result["Fri"] ?></td>
                <td><?= $result["Sat"] ?></td>
                <td><?= $result["Sun"] ?></td>
                <td><?= $result["rating"] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </form>
</div>
<?php endif; ?>

