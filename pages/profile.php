<?php
@session_start();
require_once("db_config.php");
include_once("functions.php");

if(!isLoggedIn()) {
    redirect("index.php?page=home");
}

?>

<p>Welcome to profile page</p>