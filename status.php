<?php
    require("src/headers/check_login.php");
?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Series - Status</title>
</head>
<?php session_start();
if($_SESSION["permissions"] == "ADMIN"){
    exec("cd Controll && python3 py_info.py", $output);
    foreach($output as $line){
	echo $line."<br>";
    }
}else{
    echo "Access Denied!";
}
?>
</html>
