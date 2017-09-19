<?php
    require_once("src/headers/utils.php");
    require_once("src/headers/series.php");
    
    require("src/headers/check_login.php");
    session_start();
    $query = get_url_params();
    $series_name = $query["name"];

    $cur_series = $series[$series_name];
    $cur_series_info_path = get_series_info_path($series_name);
    $cur_series_info = load_json_file($cur_series_info_path);
    $followed_series = get_followed_series();    
?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="src/style.css">
    <script type="text/javascript" src="src/js/calls.js"></script>
    <title>Series - <?php echo $series_name; ?></title>
</head>
<body>
    <?php require("header.php"); ?>
    <div class="info">
        <div class="info-title">
            <h1><?php echo $cur_series["title"]; ?> (<?php echo $cur_series["year"]; ?>)</h1>    
            <?php        
                if(in_array($cur_series["title"], $followed_series))
                    echo '<input type="button" value="Unfollow" onclick="unfollow(\''.$cur_series["title"].'\')">';
                else
                    echo '<input type="button" value="Follow" onclick="follow(\''.$cur_series["title"].'\')">'; 
            ?>
        </div>
        <div class="info-rest">
            <img src="<?php echo get_series_img_path($cur_series["title"]); ?>"/>
            <div class="info-text">
                <h3><?php echo $cur_series["description"]; ?></h3>
                <?php
                    if(array_key_exists("info", $cur_series)){
                        echo '<h4>Rating: '.$cur_series["info"]["rating"].'/10</h3>';
                            echo '<h4>Genres:';
                            $genres = "";
                            foreach($cur_series["info"]["genres"] as $genre){
                                $genres .= " ".$genre.",";
                            }
                            echo substr($genres, 0, strlen($genres)-1);
                            echo '</h4>';
                    }
                ?>
            </div>
        </div>
    </div>
    <?php
        if($cur_series_info != null){
            echo '<div class="seasons">';
            foreach($cur_series_info["seasons"] as $season){
                echo '<div class="series-table">';
                echo "<h2>Season ".$season["number"]."</h2>";
                echo '<div class="table-background">';
                echo '<table>
                        <tr class="table-header-background">
                            <th>#</th>
                            <th>name</th>
                            <th>date</th>
                        </tr>';
                foreach($season["episodes"] as $episode){
                    echo "<tr>";
                    echo "<td>".create_episode_link($cur_series["title"], $season["number"], $episode["number"], $episode["number"])."</td>";
                    echo "<td>".create_episode_link($cur_series["title"], $season["number"], $episode["number"], $episode["name"])."</td>";
                    echo "<td>".create_episode_link($cur_series["title"], $season["number"], $episode["number"], $episode["date"])."</td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo '</div>';
                echo '</div>';    
            }
            echo '</div>';
            
        }else{
            echo '<br><br><h1 class="error">No information!</h1>';
        }
    ?>
    
</body>


</html>
