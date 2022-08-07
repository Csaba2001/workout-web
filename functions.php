<?php
function redirect($URL){
    header("Location: $URL");
    echo "<script>window.location.href = '$URL';</script>";
}
function sanitize($str){
    $str = stripslashes($str);
    return $str = htmlspecialchars($str);
}
function json($str, $type = "error", $options = []){
    die(json_encode([
        "type" => $type,
        "message" => $str,
        "options" => $options
    ]));
}
function isPost(){
    return ($_SERVER["REQUEST_METHOD"] === "POST");
}
function isGet(){
    return ($_SERVER["REQUEST_METHOD"] === "GET");
}