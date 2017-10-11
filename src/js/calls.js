function follow(series) {
    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "src/routes/follow.php", true);
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhttp.send("series="+series+"&add=true");
    location.reload();
}

function unfollow(series) {
    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "src/routes/follow.php", true);
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhttp.send("series="+series+"&add=false");
    location.reload();
}

function save_priority_providers(){
    var inputs = Array.from(document.getElementsByTagName('table')[0].getElementsByTagName('input'));
    var providers = "";

    inputs.forEach(function(element) {
        if(element.checked)
            providers += element.value + ",";
    }, this);

    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "src/routes/providers.php", true);
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhttp.send("providers="+providers);

    alert("Saved!");
}

function episode_watched(){
    var url_parts = window.location.href.split("/");
    var episode_url = url_parts[url_parts.length-1];

    console.log(episode_url);

    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "src/routes/watched_episode.php", true);
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhttp.send("url="+episode_url);

    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            console.log(xhttp.responseText);
        }
    }
    
}