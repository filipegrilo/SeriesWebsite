<?php
require_once("../headers/follow.php");

session_start();
if($_POST["add"]=="true"){
    $followed_series = get_followed_series();
    add_to_followed_series($_POST["series"], $followed_series);
}elseif($_POST["add"]=="false"){
    remove_from_followed_series($_POST["series"]);
}


?>