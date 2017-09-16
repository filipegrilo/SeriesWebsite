<?php
    require_once("utils.php");
    require_once("follow.php");

    $series_path = "Data/Series.json";
    $series = load_json_file($series_path);
    
    function get_series_info_path($series_name){
        return 'Data/Series/'.$series_name.'/'.$series_name.'.json';
    }
    function create_episode_link($series_name, $season_num, $episode_num, $text){
        return '<a href="episode.php?series='.$series_name.'&season='.$season_num.'&episode='.$episode_num.'">'.$text.'</a>';
    }
?>
