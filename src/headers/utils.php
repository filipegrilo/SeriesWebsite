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
            if(self::$db == null) self::$db = new Database();
            
            return self::$db->connection;
        }
    }

    class JsonData{
        private static $data;
        private $series;

        private function __construct(){
            $this->series = load_json_file("Data/Series.json");
        }

        function __destruct(){
            $this->series = null;
        }

        public static function get_all_series(){
            if(self::$data == null) self::$data = new JsonData();
            
            return self::$data->series;
        }
    }

    function get_series_img_path($series_name){
        $path = 'Data/Series/'.$series_name.'/img/'.$series_name.'.jpg';
        if(!file_exists($path)) return 'Data/Error/no_image.gif';
        return $path;
    }

    function load_json_file($url){
        $fp = fopen($url, "r");
        $json = json_decode(fread($fp,filesize($url)), true);
        fclose($fp); 
        return $json;
    }

    function save_json_file($url, $json_content){
        $fp = fopen($url, 'w');
        fwrite($fp, json_encode($json_content));
        fclose($fp);
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

    function generate_episode_link($series, $season, $episode, $href=""){
        if($href == "") $href = 'episode.php?series='.$series.'&season='.$season.'&episode='.$episode;
        echo '<a href="'.$href.'">';
            echo '<div class="episode-link">';
                echo '<img class="series-img" src="'.get_series_img_path($series).'">';
                echo '<p>'.$series.': Season '.$season.': Episode '.$episode.'</p>';
            echo '</div>';
        echo '</a>';
    }
?>
