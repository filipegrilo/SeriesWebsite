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
    <h1><?php echo $cur_series["title"]; ?> (<?php echo $cur_series["year"]; ?>)</h1>    
    <?php        
        if(in_array($cur_series["title"], $followed_series))
            echo '<input type="button" value="Unfollow" onclick="unfollow(\''.$cur_series["title"].'\')">';
        else
            echo '<input type="button" value="Follow" onclick="follow(\''.$cur_series["title"].'\')">'; 
    ?>
    <h3><?php echo $cur_series["description"]; ?></h3>
    <img src="<?php echo get_series_img_path($cur_series["title"]); ?>"/>
    <?php
        if(array_key_exists("info", $cur_series)){
            echo '<h3>Rating: '.$cur_series["info"]["rating"].'/10</h3>';
                echo '<h4>Genres:';
                $genres = "";
                foreach($cur_series["info"]["genres"] as $genre){
                    $genres .= " ".$genre.",";
                }
                echo substr($genres, 0, strlen($genres)-1);
                echo '</h4>';
        }

        if($cur_series_info != null){
            foreach($cur_series_info["seasons"] as $season){
                echo "<h2>Season ".$season["number"]."<h2/>";
                echo "<table>
                        <tr>
                            <th>#</th>
                            <th>name</th>
                            <th>date</th>
                        </tr>";
                foreach($season["episodes"] as $episode){
                    echo "<tr>";
                    echo "<td>".create_episode_link($cur_series["title"], $season["number"], $episode["number"], $episode["number"])."</td>";
                    echo "<td>".create_episode_link($cur_series["title"], $season["number"], $episode["number"], $episode["name"])."</td>";
                    echo "<td>".create_episode_link($cur_series["title"], $season["number"], $episode["number"], $episode["date"])."</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }    
        }else{
            echo '<br><br><h1 class="error">No information!</h1>';
        }
    ?>
    
</body>


</html>
