<?php
    require_once("src/headers/utils.php");

    function generate_followed_series_links(){
        $followed_series = get_followed_series();

        echo '<h2>Followed Series:</h2>';
        foreach($followed_series as $series){
            echo '<a href="series.php?name='.$series.'">'.$series.'</a><br>';
        }
    }

    function generate_new_followed_series_episodes_links(){
        $followed_series = get_followed_series();
        $new_episodes = load_json_file("Data/New_Episodes.json");
        
        echo '<h2>New Followed Series Episodes:</h2>';
        foreach($new_episodes["episodes"] as $episode){
            if(in_array($episode["series"], $followed_series)){
                echo '<a href="episode.php?series='.$episode["series"].'&season='.$episode["season"].'&episode='.$episode["episode"].'">'.$episode["series"].': Season '.$episode["season"].': Episode '.$episode["episode"].'</a><br>';
            }
        }
    }

    function generate_new_episodes_links(){
        $new_episodes = load_json_file("Data/New_Episodes.json");

        echo '<h2>New Episodes:</h2>';
        foreach($new_episodes["episodes"] as $episode){
            echo '<a href=episode.php?series="'.$episode["series"].'&season='.$episode["season"].'&episode='.$episode["episode"].'">'.$episode["series"].': Season '.$episode["season"].': Episode '.$episode["episode"].'</a><br>';
        }
    }

    function get_last_episode(){
        if(!isset($_SESSION)) session_start();

        $conn = get_db_connection();
        $stmt = $conn->prepare('SELECT last_episode FROM users WHERE id=:id');
        $stmt->bindParam('id', $_SESSION["id"]);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result["last_episode"];
    }

    function generate_last_episode_link(){
        $last_episode_url = get_last_episode();
        $parts = parse_url($last_episode_url);
        parse_str($parts["query"], $query);
        $name = $query["series"];
        $season = $query["season"];
        $episode = $query["episode"];
        
        echo '<a href="'.$last_episode_url.'">'.$name.': Season '.$season.': Episode '.$episode.'</a>';
    }
?>