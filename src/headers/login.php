<?php
    require_once("src/headers/utils.php");

    function generate_login_form(){
        echo '<form action="index.php" method="post">
                Username:<br>
                <input type="text" name="username"><br>
                Password:<br>
                <input type="password" name="password">
                <input type="submit" value="Submit">
            </form>';
    }

    function login(){
        $conn = get_db_connection();
        $username = $_POST["username"];
        $password = sha1($_POST["password"]);

        $stmt = $conn->prepare('SELECT * FROM users WHERE username=:username AND password=:password');
        $stmt->bindParam('username', $username);
        $stmt->bindParam('password', $password);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if(count($result) == 1){
            session_start();
            $_SESSION["user"] = $username;
            $_SESSION["permissions"] = $result[0]["permissions"];
            $_SESSION["id"] = $result[0]["id"];
        }else{
            echo 'Wrong username or password';
        }
    }
?>
