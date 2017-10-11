<?php
    require_once("src/headers/utils.php");

    function generate_followed_series_links(){
        $followed_series = get_followed_series();

        echo '<h2 id="fs" name="header">Followed Series:</h2>';
        echo '<div name="content" class="episodes-display">';
        echo '<div class="episodes-display-links">';
        foreach($followed_series as $series){
            if($series == "") continue;
            echo '<a href="series.php?name='.$series.'"><div class="episode-link"><img class="series-img" src="'.get_series_img_path($series).'"><p>'.$series.'</p></div></a>';
        }
        echo '</div>';
        echo '</div>';
    }

    function generate_new_followed_series_episodes_links(){
        $followed_series = get_followed_series();
        $new_episodes = load_json_file("Data/New_Episodes.json");
        
        echo '<h2 id="nfse" name="header">New Followed Series Episodes:</h2>';
        echo '<div name="content" class="episodes-display">';
        echo '<div class="episodes-display-links">';
        foreach($new_episodes["episodes"] as $day){
            foreach($day["day_episodes"] as $episode){
                if(in_array($episode["series"], $followed_series)){
                    generate_episode_link($episode["series"], $episode["season"], $episode["episode"]);
                }
            }
        }
        echo '</div>';
        echo '</div>';
    }

    function generate_new_episodes_links(){
        $new_episodes = load_json_file("Data/New_Episodes.json");

        echo '<h2 id="nep" name="header">New Episodes:</h2>';
        echo '<div name="content" class="episodes-display">';
        foreach($new_episodes["episodes"] as $day){
            echo '<h3 id="'.$day["date"].'" name="header" h3>'.$day["date"].':</h3>';
            echo '<div name="content" class="episodes-display-links">';
            foreach($day["day_episodes"] as $episode){
                generate_episode_link($episode["series"], $episode["season"], $episode["episode"]);
            }
            echo '</div>';
        }
        echo '</div>';
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
        
        echo '<h2 id="lew" name="header">Last Episode Watched:</h2>';
        echo '<div name="content" class="episodes-display">';
        echo '<div class="episodes-display-links">';
        generate_episode_link($name, $season, $episode, $last_episode_url);
        echo '</div>';
        echo '</div>';
    }
?>
