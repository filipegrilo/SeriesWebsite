<?php
require_once("src/headers/search.php");
$series_per_page = 30;

if(isset($_GET["page"])) $start_page = $_GET["page"];
else $start_page = 0;


?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="src/style.css">
    <title>Series - Search</title>
</head>
<body>
    <?php require("header.php"); ?>
    <form method="GET" href="search.php">
        <input type="text" name="s" <?php if(isset($_GET["s"])) echo 'value="'.$_GET["s"].'"' ?>>
        <input type="submit" value="Search">
    </form>
    <div class="episodes-display">
        <div class="episodes-display-links">
        <?php
            if(!isset($_GET["s"])){
                get_series_with_pages($series, $start_page, $series_per_page);
                $num_pages = ceil(count($series) / $series_per_page);
            }else{
                $search = search_series($_GET["s"]);
                get_series_with_pages($search, $start_page, $series_per_page);
                $num_pages = ceil(count($search) / $series_per_page);
            }
        ?>
        </div>
    </div>
    <?php generate_prev_and_next_buttons($_GET["s"], $start_page, $num_pages); ?>
    <p>Page:<?php echo $start_page+1; ?>/<?php echo $num_pages; ?></p>
</body>
</html>