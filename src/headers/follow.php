<?php
require_once("utils.php");

function add_to_followed_series($series, $followed_series){
    $conn = get_db_connection();
    if(!in_array($series, $followed_series)){
        $stmt = $conn->prepare('UPDATE config SET followed_series=CONCAT(followed_series,:series,",") WHERE user_id=:id');
        $stmt->bindParam('series', $series);
        $stmt->bindParam('id', $_SESSION["id"]);
        $stmt->execute();
    }
}

function remove_from_followed_series($series){
    $conn = get_db_connection();
    $followed_series = get_followed_series();
    $result_array = array_diff($followed_series, [$series]);
    $result = "";

    foreach($result_array as $s){ 
        if($s != "") $result .= $s.",";
    }
    
    $stmt = $conn->prepare('UPDATE config SET followed_series=:followed_series WHERE user_id=:id');
    $stmt->bindParam('followed_series', $result);
    $stmt->bindParam('id', $_SESSION["id"]);
    $stmt->execute();
}
?>