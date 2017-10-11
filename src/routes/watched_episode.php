<?php
    require_once("../headers/utils.php");

    if(!isset($_SESSION)) session_start();

    $url = $_POST["url"]."&season=".$_POST["season"]."&episode=".$_POST["episode"];
    $path = "../../Data/User_Logs/".$_SESSION["id"].".json";

    //update watched last episode
    $conn = get_db_connection();
    $stmt = $conn->prepare('UPDATE users SET last_episode=:last_episode WHERE id=:id');
    $stmt->bindParam('last_episode', $url);
    $stmt->bindParam('id', $_SESSION["id"]);
    $stmt->execute();

    //update user log file
    if(file_exists($path)) $log = load_json_file($path);
    else $log = array("log" => []);

    foreach($log["log"] as $l)
        if($l == $url) return;
    
    array_unshift($log["log"], $url);

    save_json_file($path, $log);
?>