<?php
require_once("../headers/utils.php");

session_start();
$conn = get_db_connection();
$stmt = $conn->prepare('UPDATE config SET priority_providers=:priority_providers WHERE user_id=:id');
$stmt->bindParam('priority_providers', $_POST["providers"]);
$stmt->bindParam('id', $_SESSION["id"]);
$stmt->execute();
?>