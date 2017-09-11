<?php
    require_once("src/headers/settings.php");
    session_start();
    $providers = get_providers();
?>

<html>
<head>
    <script type="text/javascript" src="src/js/calls.js"></script>
</head>
<body>
    <?php echo '<form action="index.php">
                <input type="submit" value="Home" />
            </form>';
    ?>
    <h1>Priority Providers:</h1>
    <?php generate_providers_table();?>
    <input type="button" onclick="save_priority_providers()" value="Save">
</body>
</html>