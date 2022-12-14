<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");
require_once("User.php");

$user = new User();
$user = User::getCurrentUser();
if(!$user->isUser()){
    redirect("index.php?page=home");
}

global $dbh;
global $days;
$categories = getCategories();

$trainers = [];
$results = [];
$error = "";

try {
    $sql = "SELECT DISTINCT(trainings.TrainerID), trainers.rating, trainers.rated, persons.FirstName, persons.LastName FROM trainings INNER JOIN persons ON trainings.TrainerID = persons.PersonID INNER JOIN trainers ON trainings.TrainerID = trainers.TrainerID WHERE trainings.TrainerID <> 0;";
    $query = $dbh->prepare($sql);
    $query->execute();
    $trainers = $query->fetchAll(PDO::FETCH_ASSOC);
}catch(PDOException $e){
    $error = "Hiba történt";
}

if(isPost()){
    $category = sanitize($_POST['category']);
    $trainer = sanitize($_POST['trainer']);
    searchForTraining($category, $trainer);
}else{
    searchForTraining();
}

//$exercises = getExercises();

function searchForTraining($category = null, $trainer = null){
    global $dbh;
    global $trainers;
    global $results;
    global $error;

    if(!array_key_exists($category,getCategories())){
        $category = null;
    }

    if(!in_array($trainer, array_column($trainers, 'TrainerID'))){
        $trainer = null;
    }
    $personID = $_SESSION["PersonID"];

    try {
        $sql = "SELECT trainings.TrainerID, persons_trainings.PersonID, categories.CategoryName, trainings.picked, trainings.description as description, persons.FirstName, trainers.rating, t1.ExerciseName as Mon, t1.Description as MonDesc, t2.ExerciseName as Tue, t2.Description as TueDesc, t3.ExerciseName as Wed, t3.Description as WedDesc, t4.ExerciseName as Thu, t4.Description as ThuDesc, t5.ExerciseName as Fri, t5.Description as FriDesc, t6.ExerciseName as Sat, t6.Description as SatDesc, t7.ExerciseName as Sun, t7.Description as SunDesc, trainings.TrainingID
        FROM trainings 
        LEFT JOIN exercises t1 ON trainings.Mon=t1.ExerciseID
        LEFT JOIN exercises t2 ON trainings.Tue=t2.ExerciseID
        LEFT JOIN exercises t3 ON trainings.Wed=t3.ExerciseID
        LEFT JOIN exercises t4 ON trainings.Thu=t4.ExerciseID
        LEFT JOIN exercises t5 ON trainings.Fri=t5.ExerciseID
        LEFT JOIN exercises t6 ON trainings.Sat=t6.ExerciseID
        LEFT JOIN exercises t7 ON trainings.Sun=t7.ExerciseID
        LEFT JOIN categories ON trainings.CategoryID = categories.CategoryID 
        LEFT JOIN persons ON trainings.TrainerID = persons.PersonID
        LEFT JOIN trainers ON trainings.TrainerID = trainers.TrainerID
        LEFT JOIN persons_trainings ON trainings.TrainingID = persons_trainings.TrainingID WHERE ";
        $ext = "";
        if($category){
            $ext .= " categories.CategoryID=:category";
        }
        if($category && !$trainer){
            $ext .= " AND trainings.TrainerID <> 0";
        }
        if(!$category && !$trainer){
            $ext .= " trainings.TrainerID <> 0";
        }
        if($category && $trainer)$ext .= " AND";
        if($trainer){
            $ext .= " trainings.TrainerID IN(:trainerID) <> 0";
        }
        $ext .= " AND persons_trainings.PersonID IS NULL AND trainings.status = 'active'";
        $sql .= $ext;

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
            $error = "Nincs találat";
        }
    }catch(PDOException $e){
        $error = "Hiba történt";
    }
}
?>

<div class="d-flex flex-column">
    <div class="container d-flex flex-row justify-content-center">
        <form class="m-3 d-flex flex-row align-items-center" method="post" action="index.php?page=search" enctype="application/x-www-form-urlencoded">
            <span>Szűrők:</span>
            <select id="category" name="category" class="form-select m-2">
                <option>Kategória</option>
                <?php foreach($categories as $category): ?>
                <option value="<?= $category["CategoryID"] ?>"><?= $category["CategoryName"] ?></option>
                <?php endforeach; ?>
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
    <div class="alert alert-danger m-3" role="alert"><?= $error ?></div>
    <?php endif; ?>

    <div class="d-flex flex-column">
        <?php if(!empty($results)) : ?>
        <div class="container">
            <h3 class="me-auto p-4 pb-0">Találatok</h3>
            <div class="row row-cols-1 row-cols-lg-4 row-cols-md-3 g-4 m-2">
                <?php foreach($results as $result) : ?>
                    <div class="col">
                        <div class="card rounded-0">
                            <div class="card-header rounded-0 text-black text-bg-primary">
                                Aktív
                            </div>
                            <div class="card-body p-0">
                                <h5 class="card-title ps-3 pt-3 pe-3"><?= $result["description"] ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted ps-3 pe-3">
                                    edző: <?= $result["FirstName"] ?><br>
                                    kategória: <?= $result["CategoryName"] ?><br>
                                    népszerűség: <?= $result["picked"] ?>
                                </h6>
                                <ul class="list-group border-0 bg-transparent" >
                                    <?php foreach($days as $day => $dayHun) : ?>
                                        <li class="list-group-item bg-transparent border border-0 <?php $dayofweek = date("D",time()); if($day == $dayofweek) echo "active"; ?>">
                                            <div class="form-floating">
                                                <input type="text" readonly class="form-control-plaintext ps-2 pe-0" id="<?= $day ?>" value="<?= $result[$day] ?>">
                                                <label style="color: black;" class="ps-0 pe-0" for="<?= $day ?>"><?= $dayHun ?></label>
                                                <div class="text-muted">
                                                    <span>Leírás:</span>
                                                    <br>
                                                    <span><?= $result[$day."Desc"] ?></span>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <form ajax id="takeTrainingForm" name="takeTrainingForm" action="takeTraining.php" method="post" enctype="application/x-www-form-urlencoded">
                                    <div class="d-flex flex-row">
                                        <input type="hidden" name="TrainingID" id="TrainingID" value="<?= $result["TrainingID"] ?>">
                                        <input class="btn btn-success rounded-0 flex-grow-1" type="submit" value="Felvesz">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<script type="application/javascript" src="scripts/forms.js"></script>
