<?php
@session_start();
require_once("db_config.php");
require_once("functions.php");

if(!isLoggedIn()) {
    redirect("index.php?page=home");
}

?>
<div class="container col-lg-4">
    <h1 class="mt-3">Profil</h1>
    <form ajax method="post" action="profileModify.php" enctype="application/x-www-form-urlencoded">
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="modifyFirstName" name="modifyFirstName" placeholder="Keresztnev" value="<?= $user->FirstName ?>" >
            <label for="modifyFirstName">Keresztnev</label>
        </div>
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="modifyLastName" name="modifyLastName" placeholder="Vezeteknev" value="<?= $user->LastName ?>">
            <label for="modifyLastName">Vezeteknev</label>
        </div>
        <div class="form-floating mb-3">
            <input type="tel" class="form-control" id="modifyPhone" name="modifyPhone" placeholder="Telefonszam" value="<?= $user->Phone ?>">
            <label for="modifyPhone">Telefonszam</label>
        </div>
        <?php if($user->isTrainer()) : ?>
        <div class="form-floating mb-3">
            <textarea rows="12" class="h-100 form-control" id="cvText" name="cvText" placeholder="Oneletrajz"><?= $trainer->CV ?></textarea>
            <label for="cvText">Oneletrajz</label>
        </div>
        <?php endif; ?>
        <input class="btn btn-primary" type="submit" value="Modosit">
        <input class="btn btn-secondary" type="reset" value="Megsem">
        <div class="d-none alert alert-danger mt-2" role="alert">

        </div>
    </form>
    <h2 class="h1 mt-3">Jelszo</h2>
    <form ajax method="post" action="forgot.php" enctype="application/x-www-form-urlencoded">
        <div class="form-floating mb-3">
            <input type="password" class="form-control" id="forgotPassword" name="forgotPassword" placeholder="Jelszo">
            <label for="forgotPassword">Jelszo</label>
        </div>
        <div class="form-floating mb-3">
            <input type="password" class="form-control" id="forgotPasswordConfirm" name="forgotPasswordConfirm" placeholder="Jelszo megerositese">
            <label for="forgotPasswordConfirm">Jelszo megerosirtese</label>
        </div>
        <input class="btn btn-primary" type="submit" value="Modosit">
        <input class="btn btn-secondary" type="reset" value="Megsem">
        <input type="hidden" id="forgotEmail" name="forgotEmail" value="<?= $user->Email ?>">
        <div class="d-none alert alert-danger mt-2" role="alert">

        </div>
    </form>
</div>
<script type="application/javascript" src="scripts/forms.js"></script>