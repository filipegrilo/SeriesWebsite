<?php
require_once("src/headers/utils.php");

function generate_user_history(){
    $path = "Data/User_Logs/".$_SESSION["id"].".json";
    $log = load_json_file($path);
    
    echo '<h2 id="nfse" name="header">History:</h2>';
    echo '<div name="content" class="episodes-display">';
    echo '<div class="episodes-display-links">';
    foreach($log["log"] as $l){
        $parts = parse_url($l);
        parse_str($parts["query"], $query);
        $name = $query["series"];
        $season = $query["season"];
        $episode = $query["episode"];
        generate_episode_link($name, $season, $episode, $last_episode_url);
    }
    echo '</div>';
    echo '</div>';
}

?>