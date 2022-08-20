<?php
@session_start();
require_once("db_config.php");
include_once("functions.php");

$trainers = getTrainers();
?>
<div class="container p-2">
    <?php if(!$user): ?>
    <div class="container-fluid d-flex flex-row col-lg-6 mb-4">
        <div class="column">
            <h2>Eleged van ebből?</h2>
            <img class="img-fluid" src="images/man-641691_1920.jpg" alt="Pickle man">
        </div>
        <div>
            <button data-bs-toggle="modal" data-bs-target="#registerModal" role="button" class="btn btn-primary">Regisztrálj!</button>
        </div>
    </div>
    <?php endif; ?>
    <div class="container-fluid d-flex flex-column col-lg-12">
        <h2 class="h1 mb-3">Edzőink</h2>
        <?php foreach($trainers as $trainer): ?>
        <?php if($trainer["approval"] === "approved"): ?>
        <div class="row mb-4 text-break">
            <div class="d-flex flex-column col-lg-8 align-items-end">
                <h3><?= substr($trainer["LastName"],0,1).". ".$trainer["FirstName"] ?></h3>
                <p><?= substr($trainer["CV"],0,255); if(strlen($trainer["CV"]) >= 255){ echo "..."; } ?></p>
                <?php if($trainer["rating"]): ?>
                <div class="d-flex flex-row flex-wrap-0">
                <?php for($i = 0; $i < $trainer["rating"]; $i++): ?>
                    <span class="material-symbols-outlined text-warning">
                    star
                    </span>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php if($trainer["picture"]): ?>
            <div class="col-lg-4">
                <img class="img-fluid mt-2 mb-2" alt="<?= $trainer["FirstName"] ?>" src="images/<?= $trainer["picture"] ?>">
            </div>
            <?php endif; ?>
            <hr>
        </div>
        <?php endif; endforeach; ?>
    </div>
</div>