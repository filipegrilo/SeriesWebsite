<?php
require_once("src/headers/utils.php");

$series = JsonData::get_all_series();

function get_series_with_pages($series, $page_num, $series_per_page){
    if($page_num < 0) return;
    
    $keys = array_keys($series);
    $length = count($keys);
    $num_pages = ceil($length / $series_per_page);
    $start_index = $page_num * $series_per_page;
    $end_index = $page_num * $series_per_page + $series_per_page;

    $result = "";
    for($i=$start_index; $i < $end_index && $i < $length; $i++){
        $s = $series[$keys[$i]];
        $result .= '<a href="series.php?name='.$s["title"].'"><div class="episode-link"><img class="series-img" src="'.get_series_img_path($s["title"]).'"><p>'.$s["title"].'</p></div></a>';
    }   
    return $result; 
}

function generate_prev_and_next_buttons($s, $page, $num_pages){
    if($s == "") $href = '?page=';
    else $href = '?s='.$s.'&page=';

    if($page-1 >= 0)
        echo '<input type="button" value="Previous" onclick="window.location.href=\''.$href.($page-1).'\'">';
    if($page+1 < $num_pages)
        echo '<input type="button" value="Next" onclick="window.location.href=\''.$href.($page+1).'\'">';
}

function search_series($target){
    $series = JsonData::get_all_series();
    $keys = array_keys($series);
    $result = [];
    $target = strtolower($target);

    foreach($keys as $key){
        if(strpos(strtolower($key), $target) !== false) array_push($result, $series[$key]);
    }

    return $result;
}
?>