<?php
    require("src/headers/check_login.php");
    require_once("src/headers/settings.php");
    session_start();
    $providers = get_providers();
?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="src/style.css">
    <script type="text/javascript" src="src/js/calls.js"></script>
    <title>Series - Settings</title>
</head>
<body>
    <?php require("header.php"); ?>
    <h1>Priority Providers:</h1>
    <?php generate_providers_table();?>
    <input type="button" onclick="save_priority_providers()" value="Save">
</body>
</html>
