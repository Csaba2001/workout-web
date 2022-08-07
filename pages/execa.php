<?php
@session_start();
require_once("functions.php");
const SECRET = "eperfa28ha3";
require_once("db_config.php");
if(empty($_SESSION) || $_SESSION["Rank"] === 'user'){
    redirect("index.php?page=home");
    die();
}
try {
    $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER, DB_PASS,
        [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]);
} catch (PDOException $e) {
    exit("Error: " . $e->getMessage());
}
/*$asd=$_SESSION['Email'];
var_dump($asd);*/
$asd=$_SESSION['PersonID'];                                          //keresés
$sql = "SELECT trainings.Category, persons.FirstName, trainers.rating, t1.ExerciseName, t2.ExerciseName, t3.ExerciseName, t4.ExerciseName, t5.ExerciseName, t6.ExerciseName, t7.ExerciseName, trainings.TrainingID, persons.PersonID
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
$query->bindParam(':ed', $asd, PDO::PARAM_STR);
try {
    $query->execute();
    $results = $query->fetchAll();
} catch (PDOException $error) {
    die($error);
}
    /*echo "<pre>";
    var_dump($results);
    echo "</pre>";*/
?>

<div class="searchbar">
    <form method="post">
        <table class="d-flex table table-dark table-striped" style=" text-align: center; " >
            <tr >

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
                <th scope="col">Hozzáadás</th>
            </tr>


            <tr>
                <td><br/><select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" style="line-height: 30px; margin-right: 30px">
                        <option value="nocateg">Kategória</option>
                        <option value="weightloss">Fogyás</option>
                        <option value="cutting">Szálkásítás</option>
                        <option value="bulking">Erősítés</option>
                    </select></td>

                <td><textarea class="form-control" aria-label="With textarea"></textarea></td>
                <td><textarea class="form-control" aria-label="With textarea"></textarea></td>
                <td><textarea class="form-control" aria-label="With textarea"></textarea></td>
                <td><textarea class="form-control" aria-label="With textarea"></textarea></td>
                <td><textarea class="form-control" aria-label="With textarea"></textarea></td>
                <td><textarea class="form-control" aria-label="With textarea"></textarea></td>
                <td><textarea class="form-control" aria-label="With textarea"></textarea></td>
                <td><textarea class="form-control" aria-label="With textarea"></textarea></td>
                <td><textarea class="form-control" aria-label="With textarea"></textarea></td>
                <td><button name='insert'class="btn btn-danger" >INSERT</button></td>
            </tr>
        </table>
        <table class="table table-dark table-striped" style=" text-align: center;  " >
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
                if($results)
                    foreach ($results as $result) {
                        echo "<tr>
                

                <td>$result[0]</td>
                <td onclick=openWin()>Edzés leírása</td>
                <td>$result[3]</td>
                <td>$result[4]</td>
                <td>$result[5]</td>
                <td>$result[6]</td>
                <td>$result[7]</td>
                <td>$result[8]</td>
                <td>$result[9]</td>
                <td>$result[2]</td>
                <td><button name='delete' class='btn btn-danger' >DELETE</button></td>
              </tr>";
                    }
            ?>
        </table>
    </form>
</div>