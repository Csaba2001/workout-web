<?php
@session_start();
require_once("functions.php");
require_once("db_config.php");
require_once("User.php");
require_once("Trainer.php");

if(!$user->isUser()){
    redirect("index.php?page=home");
}

$trainers = [];

try {
    global $dbh;
    $sql = "SELECT trainings.TrainerID, persons.FirstName, persons.LastName, trainers.picture FROM `persons_trainings` INNER JOIN trainings ON persons_trainings.TrainingID = trainings.TrainingID INNER JOIN persons ON trainings.TrainerID = persons.PersonID INNER JOIN trainers ON trainings.TrainerID = trainers.TrainerID WHERE NOT EXISTS(SELECT 1 FROM persons_trainers_rating WHERE persons_trainers_rating.PersonID = :pid) AND persons_trainings.PersonID = :pid AND trainings.TrainerID <> 0 GROUP BY TrainerID;";
    $query = $dbh->prepare($sql);
    $query->bindParam(":pid",$user->PersonID);
    $query->execute();
    $trainers = $query->fetchAll(PDO::FETCH_ASSOC);
}catch (PDOException $e){
    json("SQL hiba: ".$e->getMessage());
}

if(isPost()){
    $rating = sanitize($_POST["rate"]);
    $trainerID = sanitize($_POST["TrainerID"]);

    if(!in_array($trainerID, array_column($trainers, "TrainerID"))){
        setAlert("Invalid trainer.");
        redirect("index.php?page=trainers");
    }
    if(!in_array($rating,[1,2,3,4,5])){
        setAlert("Invalid rating.");
        redirect("index.php?page=trainers");
    }
    try {
        $sql = "INSERT INTO persons_trainers_rating (TrainerID, PersonID, Rating) VALUES (:tid, :pid, :rating);";
        $query = $dbh->prepare($sql);
        $query->bindParam(":tid",$trainerID);
        $query->bindParam(":pid",$user->PersonID);
        $query->bindParam(":rating",$rating);
        if($query->execute()){
            setAlert("Köszönjük az értékelést.","success");
            redirect("index.php?page=home");
        }else{
            setAlert("Hiba lépett fel.");
            redirect("index.php?page=trainers");
        }
    }catch (PDOException $e){
        setAlert("Hiba lépett fel.".$e->getMessage());
        redirect("index.php?page=home");
    }
}
?>
<div class="container-fluid d-flex flex-column col-lg-12">
    <h2 class="h1 m-3 mb-5">Edzők ertekelese</h2>
    <?php
    if($trainers):
    foreach($trainers as $trainer): ?>
    <div class="row mb-4">
        <div class="d-flex flex-column col-lg-5 align-items-end">
            <h3><?= $trainer["FirstName"]."&nbsp;".$trainer["LastName"] ?></h3>
            <form ajax class="rate" method="post" action="index.php?page=trainers" enctype="application/x-www-form-urlencoded">
                <input type="hidden" name="TrainerID" id="TrainerID" value="<?= $trainer["TrainerID"] ?>">
                <?php for($i = 5; $i > 0; $i--): ?>
                <input onclick="submit(this)" type="radio" id="star<?= $i ?>" name="rate" value="<?= $i ?>">
                <label for="star<?= $i ?>" title="<?= $i ?> csillag"><?= $i ?> csillag</label>
                <?php endfor; ?>
            </form>
        </div>
        <?php if($trainer["picture"]): ?>
        <div class="col-lg-4">
            <img class="img-fluid" alt="<?= $trainer["FirstName"] ?>" src="images/<?= $trainer["picture"] ?>">
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach;
    else : ?>
    <span class="ms-4">Jelenleg nem tud edzéstervet értékelni</span>
    <?php endif; ?>
</div>