<?php
@session_start();
require_once("functions.php");
require_once("db_config.php");
require_once("User.php");
require_once("Trainer.php");

if(!$user->isAdmin()){
    redirect("index.php?page=home");
}

global $dbh;
$categories = getCategories();

?>

<div class="container col-lg-5 border border-dark border-opacity-10 p-2 mt-3 ps-3 pe-3 pb-3">
    <h2 class="mt-2">Új edzéskategória létrehozása</h2>
    <form autocomplete="off" ajax method="post" action="newCategory.php" enctype="application/x-www-form-urlencoded" novalidate>
        <div class="mb-3">
            <label for="CategoryName" class="form-label">Kategória neve:</label>
            <input placeholder="Kategória neve" type="text" class="form-control" id="CategoryName" name="CategoryName">
        </div>

        <div class="alert alert-danger mt-2" role="alert" style="display: none;"></div>

        <input type="submit" class="btn btn-primary" value="Létrehozás">
        <input class="btn btn-secondary" type="reset" value="Ürít">
    </form>
</div>
<div class="container d-flex flex-column flex-nowrap align-items-center col-lg-5 border border-dark border-opacity-10 p-2 mt-3 ps-3 pe-3 pb-3">
    <h2 class="mt-2 align-self-start">Edzéskategóriák</h2>
    <span class="align-self-start mb-2">Eddigi edzéskategóriák</span>
    <?php foreach($categories as $result) : ?>
    <form autocomplete="off" novalidate ajax class="col-lg-10 mt-2 mb-2 border border-dark border-opacity-10 p-2 p-3" action="categoryModify.php" method="post" enctype="application/x-www-form-urlencoded" id="category<?= $result["CategoryID"] ?>Form" name="category<?= $result["CategoryID"] ?>Form" >
        <input type="hidden" name="CategoryID" id="CategoryID" value="<?= $result["CategoryID"] ?>">
        <label for="CategoryName" class="form-label">Kategória neve:</label>
        <input placeholder="Kategória neve" type="text" class="form-control mb-3" id="CategoryName" name="CategoryName" value="<?= $result["CategoryName"] ?>">

        <div class="alert alert-danger mt-2 mb-2" role="alert" style="display: none;"></div>
        <input class="btn btn-sm btn-primary" type="submit" mod="Modosit" value="Módosít">
        <input class="btn btn-sm btn-danger" type="submit" mod="Torol" value="Töröl">
    </form>
    <?php endforeach; ?>
</div>
<script src="scripts/forms.js"></script>