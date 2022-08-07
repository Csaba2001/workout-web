<?php
const SECRET = "eperfa28ha3";
require_once 'db_config.php';
try {
    $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER, DB_PASS,
        [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]);
} catch (PDOException $e) {
    exit("Error: " . $e->getMessage());
}

if(isset($_POST['submit'])) {                     //keresés
    $category = $_POST['categories'];
    //echo $category;
    $categ = 0;
    $sql = "SELECT trainings.Category, persons.FirstName, trainers.rating, t1.ExerciseName, t2.ExerciseName, t3.ExerciseName, t4.ExerciseName, t5.ExerciseName, t6.ExerciseName, t7.ExerciseName, trainings.TrainingID
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
    if($category!='nocateg'){
        $sql.= " WHERE trainings.Category=:category ORDER BY trainings.Category";
        $categ=1;
    }
    else{
        $sql.= " ORDER BY trainings.Category";
    }
    $query = $dbh->prepare($sql);
    if($categ)
    $query->bindParam(':category', $category, PDO::PARAM_STR);
    try {
        $query->execute();
        $results = $query->fetchAll();
    } catch (PDOException $error) {
        die($error);
    }
    /*echo "<pre>";
    var_dump($results);
    echo "</pre>";*/
}
?>
<div style="float: left;width: 60%">
<form method="post" action="../index.php?page=workout" enctype="application/x-www-form-urlencoded">
    <blockquote class="blockquote text-center"> <label for="categories"><h1>Category</h1></label></blockquote>
    <select name="categories" class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
        <option value="nocateg">Kategória</option>
        <option value="weightloss">Fogyás</option>
        <option value="cutting">Szálkásítás</option>
        <option value="bulking">Erősítés</option>
    </select>
    <table>
        <tr>
            <div class="d-grid gap-2 col-6 mx-auto">
                <br/><input type="submit" name="submit" id="submit" class="btn btn-secondary" type="button" value="Keresés"><br/>
            </div>
        </tr>
    </table>
</form>

<div class="searchbar">
    <form method="post">
        <table class="table table-dark table-striped" style="text-align: center">
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
            <?php
            if (isset($_POST['submit'])) {
                if($results)
                foreach ($results as $result) {
                    echo "<tr>
                    <td>$result[0]</td>
                    <td onclick=openWin()>$result[1]</td>
                    <td onclick=openWin()>Edzés leírása</td>
                    <td>$result[3]</td>
                    <td>$result[4]</td>
                    <td>$result[5]</td>
                    <td>$result[6]</td>
                    <td>$result[7]</td>
                    <td>$result[8]</td>
                    <td>$result[9]</td>
                    <td>$result[2]</td>
                  </tr>";
                }
            }
            ?>
        </table>
    </form>
</div>
</div>
<div style="float: right;width: 40%">

</div>
</body>
</html>