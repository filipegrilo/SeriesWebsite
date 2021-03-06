<?php
    require("src/headers/check_login.php");

    require("src/headers/utils.php");
    require_once("src/headers/series.php");
    require_once("src/headers/providers.php");
    require_once("src/headers/episode.php");
    
    $followed_series = get_followed_series();

    $query = get_url_params();
    $name = $query["series"];
    $season = $query["season"];
    $episode = $query["episode"];

    if(!isset($_SESSION)) session_start();

    $seasons = load_json_file(get_series_info_path($name))["seasons"];
    $episode_info = $seasons[$season-1]["episodes"][$episode-1];

    $next_episode_btn = generate_next_episode_button($episode, $season, $seasons, $name);
    $prev_episode_btn = generate_prev_episode_button($episode, $season, $seasons, $name);
    $priority_providers = get_priority_providers();
    $ordered_links = order_links_by_providers_priority($episode_info["links"], $priority_providers);
?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="src/style.css">
    <script type="text/javascript" src="src/js/calls.js"></script>
    <title>Series - <?php echo $name; ?> - Season <?php echo $season; ?> - Episode <?php echo $episode; ?> (<?php echo $episode_info["name"]; ?>)</title>
</head>
<body>
    <?php require("header.php"); ?>
    <div class="info">
        <div class="info-title">
            <h1><?php echo $name; ?></h1>
            <input type="button" value="Back" onclick="window.location.href='series.php?name=<?php echo addslashes($name); ?>'">
            <?php
                if(in_array($name, $followed_series))
                    echo '<input type="button" value="Unfollow" onclick="unfollow(\''.addslashes($name).'\')">';
                else
                    echo '<input type="button" value="Follow" onclick="follow(\''.addslashes($name).'\')">';
            ?>
        </div>
        <div class="info-rest">
            <img src="<?php echo get_series_img_path($name); ?>"/>
            <div class="info-text">
                <h2>Season <?php echo $season; ?>: Episode <?php echo $episode; ?> (<?php echo $episode_info["date"]; ?>)</h2>
                <h3><?php echo $episode_info["name"]; ?></h3> 
            </div>
        </div>
    </div>
    <div class="search-footer">
        <div class="search-footer-buttons">
            <?php   echo $prev_episode_btn;
                echo $next_episode_btn;
            ?>
        </div>
    </div>
    <div class="episode-links">
        <ul>
            <?php    
            foreach($ordered_links as $link_info){
                $img_path = str_replace(" ","",$providers[$link_info["provider"]["name"]]["img_path"]);

                echo '<li style="list-style-image: url(\''.$img_path.'\')">';
                echo '<a onclick="episode_watched()" target="_blank" href="'.$link_info["link"].'">';
                echo $link_info["provider"]["name"];
                echo "</a>";
                echo "</li>";
            }
            ?>
        </ul>
    </div>
    <div class="search-footer">
        <div class="search-footer-buttons">
            <?php   echo $prev_episode_btn;
                echo $next_episode_btn;
            ?>
        </div>
    </div>
</body>
</html>
