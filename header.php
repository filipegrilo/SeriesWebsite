<div id="header" class="header">
    <form action="index.php">
        <input type="submit" value="Home" />
    </form>
    <form action="settings.php">
        <input type="submit" value="Settings" />
    </form>
    <?php 
        session_start();
        if($_SESSION["permissions"] == "ADMIN"){
                echo '<form action="status.php" target="_blank">
                <input type="submit" value="Status" />
            </form>';
        }
    ?>
    <form action="src/routes/logout.php">
        <input type="submit" value="Logout" />
    </form>

</div>