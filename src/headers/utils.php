<?php
    class Database{
	private static $db;
	private $connection;

	private function __construct(){
	    $this->connection = new PDO('mysql:dbname=SeriesManager;host=localhost', 'root', 'toor');
 	}
	
	function __destruct(){
	    $this->connection = null;
	}

	public static function getConnection(){
	    if(self::$db == null){
		self::$db = new Database();
	    }
	    return self::$db->connection;
	}
    }

    function load_json_file($url){
        $myfile = fopen($url, "r");
        $json = json_decode(fread($myfile,filesize($url)), true);
        fclose($myfile); 
        return $json;
    }

    function get_url_params(){
        $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $parts = parse_url($actual_link);
        parse_str($parts["query"], $query);
        return $query;
    }

    function get_db_connection(){
        return Database::getConnection();
    }

    function get_followed_series(){
        if(!isset($_SESSION)) session_start();

        $conn = get_db_connection();
        $stmt = $conn->prepare('SELECT followed_series FROM config WHERE id=:id');
        $stmt->bindParam('id', $_SESSION["id"]);
        $stmt->execute();
        $result = $stmt->fetch();
        return split(",",$result["followed_series"]);
    }
?>
