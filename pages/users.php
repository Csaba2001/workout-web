<?php
@session_start();
require_once("functions.php");
require_once("db_config.php");
require_once("User.php");
require_once("Trainer.php");

$persons = getPersons();
$trainers = getTrainers();
if(!$user->isAdmin()){
    redirect("index.php?page=home");
}

function returnTrainer($id){
    global $trainers;
    foreach($trainers as $trainer){
        if($trainer["TrainerID"] === $id) return $trainer;
    }
}

if(isPost()){
    if(isset($_POST["TrainerID"])){
        $trainerID = sanitize($_POST["TrainerID"]);
    }
    elseif(isset($_POST["PersonID"])){
        $personID = sanitize($_POST["PersonID"]);
    }else{
        die("Not valid POST");
    }
    $action = sanitize($_POST["submit"]);

    if(isset($personID)){
        $person = new User();
        $person = User::getFromID($personID);
        if(!$person){
            setAlert("Nincs ilyen felhasznalo.");
            redirect("index.php?page=users");
        }
        if($action === "Tiltás"){
            $person->Status = "banned";
            if($person->save()){
                setAlert("Felhasznalo bannolva.");
                redirect("index.php?page=users");
            }else{
                setAlert("Hiba lepett fel.");
                redirect("index.php?page=users");
            }
        }elseif($action === "Feloldás"){
            $person->Status = "active";
            if($person->save()){
                setAlert("Felhasznalo feloldva.","success");
                redirect("index.php?page=users");
            }else{
                setAlert("Hiba lepett fel.");
                redirect("index.php?page=users");
            }
        }else{
            setAlert("Invalid action.");
            redirect("index.php?page=users");
        }
        echo $personID.$action;
        setAlert("Error.");
        redirect("index.php?page=users");
    }elseif(isset($trainerID)){
        $trainer = new Trainer();
        $trainer = Trainer::getFromID($trainerID);
        if(!$trainer){
            setAlert("Nincs ilyen edzo.");
            redirect("index.php?page=users");
        }
        if($action === "Felfüggeszt"){
            $trainer->approval = "pending";

            if($trainer->save()){
                setAlert("Edzo tiltva.");
                redirect("index.php?page=users");
            }else{
                setAlert("Hiba lepett fel.");
                redirect("index.php?page=users");
            }
        }elseif($action === "Engedélyez"){
            $trainer->approval = "approved";
            if($trainer->save()){
                setAlert("Edzo engedelyezve.","success");
                redirect("index.php?page=users");
            }else{
                setAlert("Hiba lepett fel.");
                redirect("index.php?page=users");
            }
        }else{
            setAlert("Invalid action.");
            redirect("index.php?page=users");
        }
        setAlert("Error.");
        redirect("index.php?page=users");
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
        <td><?php if($currentTrainer) echo $currentTrainer["rating"]."/".$currentTrainer["rated"]; ?></td>
        <td>
            <form class="d-flex flex-column" id="userModForm" name="userModForm" action="index.php?page=users" method="post" enctype="application/x-www-form-urlencoded">
                <input type="hidden" name="PersonID" id="PersonID" value="<?= $person["PersonID"] ?>">
                <input type="submit" name="submit" class="btn btn-sm btn-<?= $person["Status"] === "active" ? "danger" : "success" ?> flex-grow-1" value="<?= $person["Status"] === "active" ? "Tiltás" : "Feloldás" ?>">
            </form>
        </td>
        <td>
            <?php if($person["Rank"] === "trainer"): ?>
            <form class="d-flex flex-column" id="trainerModForm" name="trainerModForm" action="index.php?page=users" method="post" enctype="application/x-www-form-urlencoded">
                <input type="hidden" name="TrainerID" id="TrainerID" value="<?= $currentTrainer["TrainerID"] ?>">
                <input type="submit" name="submit" class="btn btn-sm btn-<?= $currentTrainer["approval"] === "approved" ? "danger" : "success" ?>" value="<?= $currentTrainer["approval"] === "approved" ? "Felfüggeszt" : "Engedélyez" ?>">
            </form>
            <?php else: ?>
            &nbsp;
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</div>
