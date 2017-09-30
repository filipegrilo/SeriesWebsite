var elements = {};

function load(){
    var h2_list = document.getElementsByName("header");
    var div_list = document.getElementsByName("content");

    for(var i=0; i < h2_list.length; i++){
        var h2 = h2_list[i];
        var div = div_list[i];

        elements[h2.id] =  {"bool": true, "original_display": div.style.display, "target": div};
        h2.addEventListener("click", toggle_visibility, false);
    }
}

function toggle_visibility(e){
    e = e.target.id;

    if(elements[e]["bool"]) elements[e]["target"].style.display = 'none';
    else elements[e]["target"].style.display = elements[e]["original_display"];
    
    elements[e]["bool"] = !elements[e]["bool"];
}