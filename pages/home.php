<?php
@session_start();
require_once("db_config.php");
include_once("functions.php");

?>
<div class="container p-2">
    <div class="container-fluid d-flex flex-row col-lg-6 mb-4">
        <div class="column">
            <h2>Eleged van ebből?</h2>
            <img class="img-fluid" src="images/man-641691_1920.jpg" alt="Pickle man">
        </div>
        <div>
            <button data-bs-toggle="modal" data-bs-target="#registerModal" role="button" class="btn btn-primary">Regisztrálj!</button>
        </div>
    </div>

</div>