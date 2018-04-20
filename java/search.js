function openInfo(button) {
	
	var obj =button.parentNode;
    var info=obj.getElementsByClassName('closed')[0];
    
    obj.style.borderBottom='2px solid white';
    button.style.zIndex='6';
    info.setAttribute('class','opened');
    button.style.top=(obj.offsetHeight + info.offsetHeight - 42);
    button.setAttribute('src','../image/close.png');
    button.setAttribute('onclick','closeInfo(this)');
}

function closeInfo(button) {
	var obj =button.parentNode;
    var info=obj.getElementsByClassName('opened')[0];
    
    obj.style.borderBottom='2px solid';
    button.style.top='auto';
    button.style.zIndex='auto';
    info.setAttribute('class','closed');
    button.setAttribute('src','../image/open.png');
    button.setAttribute('onclick','openInfo(this)');
}

function ajax (method,url,callback,data) {
    var request;
    try {
        request = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
        try {
            request = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (E) {
            request = false;
          }
      }
    if (!request && typeof XMLHttpRequest!='undefined') {
        request = new XMLHttpRequest();
    }
    if (!request)
        alert("Error initializing XMLHttpRequest!");   
     
    request.open(method,url,true);
    request.onreadystatechange = function () {
 
        if (request.readyState == 4){
            callback.call(request.responseText);
        }
    }
    if (method=='POST') {
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    }
    request.send(data);    

}



function changeValues (obj,val,div) {

    if (obj=='qam_lum' || obj=='sat_lum') {
        var lum = div.getElementsByTagName('a')[0].innerHTML;
        var href = div.getElementsByTagName('a')[0].getAttribute('href');
        div.innerHTML='<input style="width:80px;" type="text" maxlength="10" value="'+lum+'"> <input  type="text" value="'+href+'">';
        div.removeAttribute('ondblclick');
        var nameBox = div.getElementsByTagName('input')[0];
        var hrefBox = div.getElementsByTagName('input')[1];
        hrefBox.setAttribute('onkeydown',"setMuxValue(event,this,'"+obj+"','"+val+"')");
    }
    else {
        var oldValue=div.innerHTML;
        div.innerHTML='<input type="text" maxlength="50" value="'+oldValue+'">';
        div.removeAttribute('ondblclick');
        var textBox = div.getElementsByTagName('input')[0];
        textBox.setAttribute('onkeydown',"setMuxValue(event,this,'"+obj+"','"+val+"')");        
    }

}

function setMuxValue(e,i,o,v){

    if (e.keyCode=='13') {

        var parent=i.parentNode;
        var data=i.value;

        if (o=='qam_lum' || o=='sat_lum') {
            var href=i.value;
            var name=parent.getElementsByTagName('input')[0].value;
            
            /* Строки с данными в муксе и на канале немного отличаются */
            if (o=='qam_lum') {
                var subtext = 'Lum: ';
                ajax('GET',"forms/change_value.php?cp="+cp()+"&value="+v+"&data="+name+"&object=qam_name",function(){});
                ajax('GET',"forms/change_value.php?cp="+cp()+"&value="+v+"&data="+href+"&object=qam_href",function(){});
            
            }
            if (o=='sat_lum') {
                var subtext = 'Источник: ';
                ajax('GET',"forms/change_value.php?cp="+cp()+"&value="+v+"&data="+name+"&object=sat_name",function(){});
                ajax('GET',"forms/change_value.php?cp="+cp()+"&value="+v+"&data="+href+"&object=sat_href",function(){});
            
            }    
            parent.innerHTML=subtext+"<a href="+href+" target='_blank'>"+name+"</a>";
            parent.setAttribute('ondblclick',"changeValues('"+o+"','"+v+"',this)");

        }
        else {
            var data=i.value;
            ajax('GET',"forms/change_value.php?cp="+cp()+"&value="+v+"&data="+data+"&object="+o,function(){});
            parent.innerHTML=data;
            parent.setAttribute('ondblclick',"changeValues('"+o+"','"+v+"',this)");            
        }

    }
}