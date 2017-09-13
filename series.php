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
?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="src/style.css">
    <script type="text/javascript" src="src/js/calls.js"></script>
</head>
<body>
    <?php
        echo '<form action="index.php">
                <input type="submit" value="Home" />
            </form>';
        generate_series_html($cur_series, $cur_series_info);
    ?>
</body>


</html>
