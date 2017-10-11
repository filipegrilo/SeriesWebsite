<?php
    require("src/headers/history.php");
?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="src/style.css">
    <script type="text/javascript" src="src/js/home.js"></script>
    <title>Series - History</title>
</head>
<body onload="load()">
    <?php require("header.php"); ?>
    <?php generate_user_history(); ?>
</body>
</html>