<?php
@session_start();
require_once("functions.php");
require_once("db_config.php");
require_once("User.php");
require_once("Trainer.php");

foreach(getTrainers() as $trainer): ?>
<ul>
    <li><?= $trainer["FirstName"]?></li>
</ul>
<?php endforeach; ?>