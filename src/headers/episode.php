<?php
    require_once("utils.php");

    function generate_next_episode_button($episode, $season, $seasons, $name){
        $num_seasons = count($seasons);
        $num_episodes = count($seasons[$season-1]["episodes"]);

        if($episode+1 > $num_episodes){
            if($season+1 <= $num_seasons){
                return '<input type="button" value="Next Episode" onclick="window.location.href=\'?series='.addslashes($name).'&season='.($season+1).'&episode='.(1).'\'">';
            }else{
                return '';
            }
        }else{
            return '<input type="button" value="Next Episode" onclick="window.location.href=\'?series='.addslashes($name).'&season='.$season.'&episode='.($episode+1).'\'">';
        }
    }

    function generate_prev_episode_button($episode, $season, $seasons, $name){
        $num_seasons = count($seasons);

        if($episode-1 <= 0){
            if($season-1 > 0){
                $num_episodes = count($seasons[$season-2]["episodes"]);                
                return '<input type="button" value="Previous Episode" onclick="window.location.href=\'?series='.addslashes($name).'&season='.($season-1).'&episode='.($num_episodes).'\'">';
            }else{
                return '';
            }
        }else{
            return '<input type="button" value="Previous Episode" onclick="window.location.href=\'?series='.addslashes($name).'&season='.$season.'&episode='.($episode-1).'\'">';
        }
    }

    function update_last_episode($url){
        $conn = get_db_connection();
        $stmt = $conn->prepare('UPDATE users SET last_episode=:last_episode WHERE id=:id');
        $stmt->bindParam('last_episode', $url);
        $stmt->bindParam('id', $_SESSION["id"]);
        $stmt->execute();
    }

    function get_priority_providers(){
        $conn = get_db_connection();
        $stmt = $conn->prepare('SELECT priority_providers FROM config WHERE user_id=:id');
        $stmt->bindParam('id', $_SESSION["id"]);
        $stmt->execute();

        $priority_providers = $stmt->fetchAll()[0];
        return split(',', $priority_providers["priority_providers"]);
    }

    function order_links_by_providers_priority($links, $providers){
        $priority = [];
        $other = [];

        foreach($links as $link){
            $provider = $link["provider"]["name"];
            foreach($providers as $p){
                if($provider == $p){
                    array_push($priority,$link);
                    continue 2;
                }
            }
            array_push($other,$link);
        }

        return array_merge($priority, $other);
    }
?>