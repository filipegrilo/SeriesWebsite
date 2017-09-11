<?php
require_once("src/headers/episode.php");
require_once("src/headers/utils.php");



function get_providers(){
    return load_json_file("Config/Providers.json");
}

function generate_providers_table(){
    $priority_providers = get_priority_providers();
    $providers = get_providers();
    $count = 0;

    echo '<table>';
    foreach($providers as $provider){
        $count++;
        $checked="";
        if(in_array($provider["name"], $priority_providers)) $checked = "checked";
        echo '<tr>';
        echo '<td><input type="checkbox" name="providers" value="'.$provider["name"].'" '.$checked.'></td>';
        echo '<td>'.$provider["name"].'</td>';
        echo '<td><img src="'.$provider["img_path"].'"></td>';
    }
    echo '</table>';
}
?>