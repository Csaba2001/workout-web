<?php
@session_start();
require_once("functions.php");
require_once("db_config.php");
require_once("User.php");
require_once("Trainer.php");

$persons = getPersons();
$trainers = getTrainers();
/*$user = User::getCurrentUser();
if(!$user->isAdmin()){
    redirect("index.php?page=home");
}*/

function returnTrainer($id){
    global $trainers;
    foreach($trainers as $trainer){
        if($trainer["TrainerID"] === $id) return $trainer;
    }
}
?>
<div class="col-lg-16 m-4">
<h2>Felhasználók</h2>
<table class="table table-sm table-striped">
    <tr>
        <th>ID</th>
        <th>Családnév</th>
        <th>Név</th>
        <th>Email</th>
        <th>Szint</th>
        <th>Értékelés</th>
        <th>Műveletek</th>
        <th>Edzőknek</th>
    </tr>
    <?php foreach ($persons as $person): ?>
    <?php
    $currentTrainer = returnTrainer($person["PersonID"]);
    ?>
    <tr>
        <td><?= $person["PersonID"]?></td>
        <td><?= $person["LastName"]?></td>
        <td><?= $person["FirstName"]?></td>
        <td><?= $person["Email"]?></td>
        <td><?php
            switch($person["Rank"]){
                case "user":
                    echo "Felhasználó";
                    break;
                case "trainer":
                    echo "Edző";
                    break;
                default:
                    echo "Admin";
                    break;
            }
            ?>
        </td>
        <td>
            <div class="d-flex flex-column">
                <a href="#" class="btn btn-sm btn-<?= $person["Status"] === "active" ? "danger" : "success" ?> flex-grow-1"><?= $person["Status"] === "active" ? "Tiltás" : "Feloldás" ?></a>
            </div>
        </td>
        <td>
            <div class="d-flex flex-column">
                <?php if($person["Rank"] === "trainer"): ?>
                <a href="#" class="btn btn-sm btn-<?= $currentTrainer["approval"] === "approved" ? "danger" : "success" ?>"><?= $currentTrainer["approval"] === "approved" ? "Felfüggeszt" : "Engedélyez" ?></a>
                <?php else: ?>
                &nbsp;
                <?php endif; ?>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</div>
