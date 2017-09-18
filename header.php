<div class="header-main">
    <div class="header header-left">
        <form action="index.php">
            <input type="submit" value="Home" />
        </form>
        <form action="search.php">
            <input type="submit" value="Series" />
        </form>
    </div>
    <div class="header header-right">
         <form action="src/routes/logout.php">
            <input type="submit" value="Logout" />
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
    </div>
</div>