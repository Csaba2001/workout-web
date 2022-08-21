<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");

if(!$user) {
    redirect("index.php?page=home");
}

?>
<div class="container col-lg-4 border bg-white border-dark border-opacity-10 p-2 mt-3 ps-3 pe-3 pb-3">
    <h2 class="mt-3">Felhasználói adatok</h2>
    <form ajax method="post" action="profileModify.php" enctype="application/x-www-form-urlencoded">
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="FirstName" name="FirstName" placeholder="Utónév" value="<?= $user->FirstName ?>" >
            <label for="FirstName">Utónév</label>
        </div>
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="LastName" name="LastName" placeholder="Családnév" value="<?= $user->LastName ?>">
            <label for="LastName">Családnév</label>
        </div>
        <div class="form-floating mb-3">
            <input type="tel" class="form-control" id="Phone" name="Phone" placeholder="Telefonszám" value="<?= $user->Phone ?>">
            <label for="Phone">Telefonszám</label>
        </div>
        <?php if($user->isTrainer()) : ?>
        <div class="form-floating mb-3">
            <textarea rows="12" class="h-100 form-control" id="CV" name="CV" placeholder="Önéletrajz"><?= $trainer->CV ?></textarea>
            <label for="CV">Önéletrajz</label>
        </div>
        <?php endif; ?>
        <input class="btn btn-primary" type="submit" value="Módosít">
        <input class="btn btn-secondary" type="reset" value="Ürít">
        <div class="d-none alert alert-danger mt-2" role="alert">

        </div>
    </form>
    <h2 class="mt-3">Jelszó változtatás</h2>
    <form ajax method="post" action="forgot.php" enctype="application/x-www-form-urlencoded">
        <div class="form-floating mb-3">
            <input type="password" class="form-control" id="Hash" name="Hash" placeholder="Jelszó">
            <label for="Hash">Jelszó</label>
        </div>
        <div class="form-floating mb-3">
            <input type="password" class="form-control" id="PasswordConfirm" name="PasswordConfirm" placeholder="Jelszó megerősítése">
            <label for="PasswordConfirm">Jelszó megerősítése</label>
        </div>
        <input class="btn btn-primary" type="submit" value="Módosít">
        <input class="btn btn-secondary" type="reset" value="Ürít">
        <input type="hidden" id="Email" name="Email" value="<?= $user->Email ?>">
        <div class="d-none alert alert-danger mt-2" role="alert">

        </div>
    </form>
</div>
<script type="application/javascript" src="scripts/forms.js"></script>