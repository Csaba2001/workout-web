<?php
@session_start();
require_once("db_config.php");
include_once("functions.php");

if(!isLoggedIn()) {
    redirect("index.php?page=home");
    die();
}

?>
<div class="container m-2">
    <p>Welcome to the home page</p>
</div>