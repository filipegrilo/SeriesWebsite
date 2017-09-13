<?php
    require_once("src/headers/home.php");
    require_once("src/headers/login.php");
    require_once("src/headers/utils.php");
    require_once("src/headers/series.php");

    if(!isset($_SESSION)) session_start();
    
    function main($series){
        echo 'Welcome '.$_SESSION["user"];
        echo '<form action="settings.php">
                <input type="submit" value="Settings" />
            </form>';
	echo '<form action="src/routes/logout.php">
		<input type="submit" value="Logout" />
	    </form>';
        echo '<br>Last Episode: '; 
        generate_last_episode_link();
        echo '<br>';
        generate_new_followed_series_episodes_links();
        echo '<br>';
        generate_followed_series_links();
        echo '<br>';
        generate_new_episodes_links();
    }
?>

<html>
<head></head>
<body>
    <?php
        if(!isset($_SESSION["user"])){
            if(!isset($_POST["username"])){
                generate_login_form();
            }else{
                login();
                main($series);
            } 
        }else{
           main($series);
        }
    ?>
</body>
</html>
