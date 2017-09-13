<?php
    require("src/headers/check_login.php");
?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
</head>
<?php session_start();

if($_SESSION["permissions"] == "ADMIN"){
    exec("python3 py_info.py", $output);
    foreach($output as $line){
	echo $line."<br>";
    }
}else{
    echo "Access Denied!";
}
?>
</html>
