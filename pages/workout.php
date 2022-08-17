<?php
@session_start();
require_once("functions.php");
require_once("db_config.php");

if(!isLoggedIn()){
    redirect("index.php?page=home");
}

global $dbh;
global $days;
global $categories;

$personId = $_SESSION['PersonID'];
$exercises = getExercises();

if(isTrainer()) {
    try {
        $sql = "SELECT trainings.Category, persons.FirstName, trainings.picked, trainings.status, trainings.description as description, t1.ExerciseName as Mon, t2.ExerciseName as Tue, t3.ExerciseName as Wed, t4.ExerciseName as Thu, t5.ExerciseName as Fri, t6.ExerciseName as Sat, t7.ExerciseName as Sun, trainings.TrainingID, persons.PersonID
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
        WHERE persons.PersonID=:ed ORDER BY trainings.picked DESC";
        $query = $dbh->prepare($sql);
        $query->bindParam(':ed', $personId);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e) {
        $error = "Hiba tortent".$e->getMessage();
    }
}
if(isUser()){
    try {
        $sql = "SELECT * FROM trainings WHERE TrainerID <> 0 AND status = 'active';";
        $query = $dbh->prepare($sql);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        $sql = "SELECT persons.FirstName, trainings.* FROM trainings INNER JOIN persons_trainings ON persons_trainings.TrainingID = trainings.TrainingID INNER JOIN persons ON trainings.TrainerID = persons.PersonID WHERE persons_trainings.PersonID = :pid AND TrainerID <> 0;";
        $query = $dbh->prepare($sql);
        $query->bindParam(':pid', $personId);
        $query->execute();
        $selfTrainings = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach($selfTrainings as $selfTraining){
            if($selfTraining["TrainerID"] === 0){
                $selfTraining["self"] = 1;
            }
        }
    }
    catch (PDOException $e) {
        $error = "Hiba tortent".$e->getMessage();
    }
}



?>

<?php if(isset($error) && !empty($error)) : ?>
    <div class="alert alert-danger mt-2" role="alert"><?= $error ?></div>
<?php endif; ?>

<div class="d-flex flex-column">
    <?php if(isUser() && isset($selfTrainings)) : ?>
    <div class="container">
        <h3 class="me-auto p-4 pb-0">Valasztott edzéstervek</h3>
        <div class="row row-cols-1 row-cols-lg-4 row-cols-md-3 g-4 m-2">
        <?php foreach($selfTrainings as $result) : ?>
            <div class="col">
                <div class="card rounded-0">
                    <div class="card-header rounded-0 text-bg-primary">
                        Aktív
                    </div>
                    <div class="card-body p-0">
                        <form>
                            <input type="hidden" name="TrainingID" id="TrainingID" value="<?= $result["TrainingID"] ?>">
                            <h5 class="card-title ps-3 pt-3 pe-3"><?= $result["description"] ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted ps-3 pe-3">
                                kategória: <?= $categories[$result["Category"]] ?><br>
                                népszerűség: <?= $result["picked"] ?>
                            </h6>
                            <ul class="list-group border-0 bg-transparent" >
                                <?php foreach($days as $day => $dayHun) : ?>
                                    <li class="list-group-item bg-transparent border border-0 <?php $dayofweek = date("D",time()); if($day == $dayofweek) echo "active"; ?>">
                                        <div class="form-floating">
                                            <select class="form-select" id="<?= $day ?>" name="<?= $day ?>">
                                                <?php foreach($exercises as $exercise) : ?>
                                                    <option <?php if($result[$day] === $exercise["ExerciseName"]) echo "selected"; ?> value="<?= $exercise["ExerciseID"] ?>"><?= $exercise["ExerciseName"] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label style="color: black;" for="<?= $day ?>"><?= $dayHun ?></label>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="d-flex flex-row">
                                <input class="btn btn-success rounded-0 flex-grow-1" type="submit" mod="Modosit" value="Módosít">
                                <input class="btn btn-danger rounded-0" type="submit" mod="Torol" value="Töröl">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="container">
        <h3 class="me-auto p-4 pb-0">Edzéstervek</h3>
        <div class="row row-cols-1 row-cols-lg-4 row-cols-md-3 g-4 m-2">
        <?php if(isset($results)) : ?>
            <?php foreach($results as $result) : ?>
            <?php
                if($result["status"] === "active") $banned = 0;
                else $banned = 1;
            ?>
            <div class="col">
                <div class="card rounded-0 <?= $banned ? "border-danger bg-danger bg-opacity-50" : "border-primary" ?>">
                    <?php if($banned) : ?>
                    <div class="card-header rounded-0 text-bg-danger">
                        Letiltva
                    </div>
                    <?php else : ?>
                    <div class="card-header rounded-0 text-bg-primary">
                        Aktív
                    </div>
                    <?php endif; ?>
                    <div class="card-body p-0">
                        <form ajax id="modifyTrainingForm" name="modifyTrainingForm" action="trainingModify.php" method="post" enctype="application/x-www-form-urlencoded">
                            <input type="hidden" name="TrainingID" id="TrainingID" value="<?= $result["TrainingID"] ?>">
                            <input type="hidden" name="Category" id="Category" value="<?= $result["Category"] ?>">
                            <input type="hidden" name="Description" id="Description" value="<?= $result["description"] ?>">
                            <h5 class="card-title ps-3 pt-3 pe-3"><?= $result["description"] ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted ps-3 pe-3">
                                kategória: <?= $categories[$result["Category"]] ?><br>
                                népszerűség: <?= $result["picked"] ?>
                            </h6>
                            <ul class="list-group border-0 bg-transparent" >
                                <?php foreach($days as $day => $dayHun) : ?>
                                <li class="list-group-item bg-transparent border border-0 <?php $dayofweek = date("D",time()); if($day == $dayofweek) echo "active"; ?>">
                                    <div class="form-floating">
                                        <select class="form-select" id="<?= $day ?>" name="<?= $day ?>" <?= $banned ? "disabled" : "" ?>>
                                            <?php foreach($exercises as $exercise) : ?>
                                            <option <?php if($result[$day] === $exercise["ExerciseName"]) echo "selected"; ?> value="<?= $exercise["ExerciseID"] ?>"><?= $exercise["ExerciseName"] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label style="color: black;" for="<?= $day ?>"><?= $dayHun ?></label>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="alert alert-danger mt-2" role="alert" style="display: none;">

                            </div>
                            <div class="d-flex flex-row">
                                <input class="btn btn-success rounded-0 flex-grow-1" type="submit" mod="Modosit" value="Módosít" <?= $banned ? "disabled" : "" ?>>
                                <input class="btn btn-danger rounded-0" type="submit" mod="Torol" value="Töröl" <?= $banned ? "disabled" : "" ?>>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else : ?>
            <h4>Nincs meg edzesterve</h4>
            <a href="#newTrainingForm" class="btn btn-primary">Edzesterv letrehozasa</a>
            <?php if(isUser()) : ?>
                <a href="index.php?page=search" class="btn btn-primary">Edzesterv kiválasztása</a>
            <?php endif; ?>
        <?php endif; ?>
        </div>
    </div>

    <div class="container card col-lg-4 col-sm-10 p-3">
        <?php if(!empty($exercises)): ?>
        <form ajax id="newTrainingForm" name="newTrainingForm" action="newtraining.php" method="post" enctype="application/x-www-form-urlencoded">
            <h3>Edzésterv létrehozása</h3>
            <div class="form-floating mb-3">
                <select class="form-select" id="trainingCategory" name="trainingCategory">
                    <?php foreach($categories as $categoryEng => $categoryHun) : ?>
                    <option value="<?= $categoryEng ?>"><?= $categoryHun ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="trainingCategory">Kategória</label>
            </div>

            <div class="form-floating mb-3">
                <textarea class="form-control" placeholder="Edzes leirasa" id="description" name="description"></textarea>
                <label for="description">Edzés leírása</label>
            </div>

            <?php foreach($days as $day => $dayHun): ?>
                <div class="form-floating mb-3">
                    <select class="form-select" id="<?= $day ?>" name="<?= $day ?>">
                        <?php foreach($exercises as $exercise) : ?>
                        <option value="<?= $exercise["ExerciseID"] ?>"><?= $exercise["ExerciseName"] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="<?= $day ?>"><?= $dayHun ?></label>
                </div>
            <?php endforeach; ?>
            <input type="submit" class="btn btn-primary" value="Létrehoz">
            <input type="reset" class="btn btn-secondary" value="Mégsem">
            <div class="alert alert-danger mt-2" role="alert" style="display: none;"></div>
        </form>
        <?php else : ?>
            <h3>Nincs egy gyakorlata sem, készítsen párat...</h3>
            <a href="index.php?page=execa" class="btn btn-primary">Uj gyakorlat</a>
        <?php endif; ?>
    </div>
</div>


<script src="scripts/forms.js"></script>
