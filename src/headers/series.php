<?php
    require_once("utils.php");
    require_once("follow.php");

    $series_path = "Data/Series.json";
    $series = load_json_file($series_path);
    
    function get_series_info_path($series_name){
        return 'Data/Series/'.$series_name.'/'.$series_name.'.json';
    }

    function get_series_img_path($series_name){
        return 'Data/Series/'.$series_name.'/img/'.$series_name.'.jpg';
    }

    function create_episode_link($series_name, $season_num, $episode_num, $text){
        return '<a href="episode.php?series='.$series_name.'&season='.$season_num.'&episode='.$episode_num.'">'.$text.'</a>';
    }

    function generate_series_html($series, $series_info){
        $followed_series = get_followed_series();
        echo "<h1>".$series["title"]." (".$series["year"].")</h1>";
        if(in_array($series["title"], $followed_series))
            echo '<input type="button" value="Unfollow" onclick="unfollow(\''.$series["title"].'\')">';
        else
            echo '<input type="button" value="Follow" onclick="follow(\''.$series["title"].'\')">'; 
        echo "<h3>".$series["description"]."</h3>";
        echo '<img src="'.get_series_img_path($series["title"]).'"/>';
        
	if(array_key_exists("info", $series)){
	    echo '<h3>Rating: '.$series["info"]["rating"].'/10</h3>';
            echo '<h4>Genres:';
            foreach($series["info"]["genres"] as $genre){
            	echo " ".$genre.",";
            }
            echo '</h4>';
	}

        if($series_info != null){
            foreach($series_info["seasons"] as $season){
                echo "<h2>Season ".$season["number"]."<h2/>";
                echo "<table>
                        <tr>
                            <th>#</th>
                            <th>name</th>
                            <th>date</th>
                        </tr>";
                foreach($season["episodes"] as $episode){
                    echo "<tr>";
                    echo "<td>".create_episode_link($series["title"], $season["number"], $episode["number"], $episode["number"])."</td>";
                    echo "<td>".create_episode_link($series["title"], $season["number"], $episode["number"], $episode["name"])."</td>";
                    echo "<td>".create_episode_link($series["title"], $season["number"], $episode["number"], $episode["date"])."</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }    
        }else{
            echo '<br><br><h1 class="error">No information!</h1>';
        }
    }
?>
