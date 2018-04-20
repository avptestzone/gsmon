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

function showForm(img){
    
    var formName = img.getAttribute("src").match(/\/(.*)\.png/i);
    var uri = "forms/show_"+ formName[1] +"_form.php?cp="+cp(); 

    closeForm(img);

    img.setAttribute("onclick", "closeForm(this)"); 
    ajax ('GET',uri,function(){img.parentNode.innerHTML += this}); 

}

function closeForm (image){
    
    var row = image.parentNode;
    var forms = row.getElementsByClassName("forms");
    var images = row.getElementsByTagName("img");

    if (forms[0]) {
        row.removeChild(forms[0]);
    }

    for (i=0;i<images.length;i++) {
        var click;

        if(click = images[i].getAttribute("onclick")){
            if (click.match(/closeForm/)) {
                images[i].setAttribute("onclick","showForm(this)");        
            } 
        }
  
    }   
}


function sendForm (img) {

    var name = img.getAttribute("id");
    var uri="base/"+name+".php";

    var input = img.parentNode.getElementsByTagName("input");
    var select = img.parentNode.getElementsByTagName("select");
    var inputData=[];  
    var selectData=[];  

    if (input){
        for (i=0;i<input.length;i++) {
            inputData[i]=input[i].getAttribute('name')+"="+input[i].value;
        }    
    } 
    
    if (select){
        for (i=0;i<select.length;i++) {
            var selectedOptions = [];
            for (k=0;k<select[i].options.length;k++){
                if (select[i].options[k].selected){
                   selectedOptions.push(select[i].options[k].value);
                } 
            }
            selectData[i]=select[i].getAttribute('name')+"="+selectedOptions.join(',');
        }    
    } 
    
    var data=inputData.concat(selectData).concat("current_point="+cp());

    ajax('POST',uri,function(){img.parentNode.innerHTML=this},data.join("&")); 

}


function refresh_page() {
    location.reload();
}


function cp(){
    var cp;
    
    try {
        cp=document.getElementsByClassName('current_point')[0].getAttribute('id');
        return cp;
    }
    catch (e){
        try {
            cp=document.getElementByClassName('alarm_open')[0].getAttribute('id');
            return cp;
        }
        catch (E){
            return '0';
        }
        
    }

}

function showMux(currentInput){

    var muxDIV=currentInput.parentNode;
    
    while (muxDIV.className!='mux'){
        muxDIV=muxDIV.parentNode;      
    }
    
    var mux=muxDIV.id;

    currentInput.setAttribute("src", "image/minus_small.png");
    currentInput.setAttribute("onclick", "hideMux(this)");
    var uri="build/thumbnails.php?mux="+mux+"&cp="+cp();
    ajax('GET',uri,function(){loadThumbs(muxDIV,mux,this)});
}


function loadThumbs (div,num,response) {
    var urlArray=new Array();
    div.innerHTML+=response;
    
    var thumbnails=div.getElementsByClassName('thumbnails');
    imgArray=thumbnails[0].getElementsByTagName('img'); 
    
    for (i=1;i<imgArray.length;i++) {
        urlArray[i]=imgArray[i].src;
    }
 
    window.intervalArray[num]=setInterval(reloadThumb,10000,imgArray,urlArray);

}

function reloadThumb (iA,uA) {
    for (k=1;k<iA.length;k++) {
        iA[k].src=uA[k]+"?"+Math.random();
    } 
}



function hideMux (i){

    var muxDIV=i.parentNode;

    while (muxDIV.className!='mux'){
        muxDIV=muxDIV.parentNode;      
    }
    
    var muxInfo = muxDIV.getElementsByClassName('mux_info')[0];
    
    if(muxInfo){muxDIV.removeChild(muxInfo)};

    muxDIV.removeChild(muxDIV.getElementsByClassName('thumbnails')[0]);
    i.setAttribute("src", "image/plus_small.png");
    i.setAttribute("onclick", "showMux(this)");
    clearInterval(intervalArray[muxDIV.id]);
    document.getElementById("data_box").innerHTML='';
    document.getElementById("data_box").style.display = 'none';
    document.getElementById("triger_box").style.display = 'block';
}


function scanMux (mux,img) {
    var uri="forms/scan.php?mux="+mux+"&cp="+cp();
    ajax('GET',uri,function(){});

    img.setAttribute("src", "image/refresh_light_small.png");
    img.removeAttribute("onclick");
}


function status (jsonResponse) {
    var data = JSON.parse(jsonResponse);
    var trigerBox=document.getElementById('triger_box');

    for(i=0;i<data.length;i++){
        
        if(data[i].name == 'databox'){
            if(data[i].error != null){ 
                trigerBox.innerHTML = '<br><h3>Обратить внимание!</h3><p>' + data[i].error + "<img id='ct' src='image/minus_small.png' align='right' onclick='clearCPH()'>";                
            }
            else{
                trigerBox.innerHTML = '';
            }
        } 
        else{
            if(data[i].color){
                var color=data[i].color;
            }
            else {
                var color='#66ff00';
            }
        
            var mux=document.getElementById(data[i].name);
            var stolbec = mux.getElementsByClassName("mstatus");
        
            mux.setAttribute("style", "background:"+color);
            stolbec[0].innerHTML = data[i].error;
            
            var scanButton = mux.getElementsByClassName('scan_button');
 
            if (data[i].scan==1){
                scanButton[0].setAttribute('src','image/refresh_light_small.png');
                scanButton[0].style.cursor = 'default';
            }
            else {
                scanButton[0].setAttribute('src','image/refresh_dark_small.png');
                scanButton[0].setAttribute('onclick','scanMux('+data[i].name+',this)');
                scanButton[0].style.cursor = 'pointer';   
            }

        }
    }
}


function showAllMux() {
    
    var muxes = document.getElementsByClassName("mux");
    for(i=0;i<muxes.length;i++){
        var num = muxes[i].getAttribute('id');
        var img = muxes[i].getElementsByClassName("show_button");
        if(img[0].src.match(/minus_small.png/)) {continue};
        showMux(img[0]);
    }

}

function hideAllMux() {
    location.reload();
}

function scanAllMux() {
 
    var muxes = document.getElementsByClassName("mux");
    for(i=0;i<muxes.length;i++){
        var num = muxes[i].getAttribute('id');
        var img = muxes[i].getElementsByClassName("scan_button");
        if(img[0].src.match(/refresh_light_small.png/)){continue};
        scanMux(num,img[0]);
    }
    alert('Транспондеры будут обновлены в течении минуты');  
}


function epg (cid) {
    ajax('GET',"forms/show_epg.php?cp="+cp()+"&cid="+cid,function (){showWindow(this);});
}

function showWindow (content) {
    var overlay=document.getElementById('overlay');
    var wrapp=document.getElementById('wrapp');
    wrapp.setAttribute("style", "display:block; opacity: 1");
    overlay.setAttribute("style", "display:block;");
    wrapp.innerHTML=content;   
}

function closeWindow () {
    var overlay=document.getElementById('overlay');
    var wrapp=document.getElementById('wrapp');
    wrapp.setAttribute("style", "display:none; opacity: 0;");
    wrapp.innerHTML='';
    overlay.setAttribute("style", "display:none;");
    wrapp.innerHTML=content;   
}


function deleteChannel(cid,img){
    ajax('GET',"forms/delete_channel.php?cp="+cp()+"&cid="+cid,function(){});
    var thumb =img.parentNode;
    var mux =thumb.parentNode;
    mux.removeChild(thumb)

}


function changeValues (obj,val,div) {

    if (obj=='qam_lum' || obj=='sat_lum') {
        var lum = div.getElementsByTagName('a')[0].innerHTML;
        var href = div.getElementsByTagName('a')[0].getAttribute('href');
        div.innerHTML='<input style="width:80px;" type="text" maxlength="20" value="'+lum+'"> <input  type="text" value="'+href+'">';
        div.removeAttribute('ondblclick');
        var nameBox = div.getElementsByTagName('input')[0];
        var hrefBox = div.getElementsByTagName('input')[1];
        hrefBox.setAttribute('onkeydown',"setValue(event,this,'"+obj+"','"+val+"')");
    }
    else {
        var oldValue=div.innerHTML;
        div.innerHTML='<input type="text" maxlength="50" value="'+oldValue+'">';
        div.removeAttribute('ondblclick');
        var textBox = div.getElementsByTagName('input')[0];
        textBox.setAttribute('onkeydown',"setValue(event,this,'"+obj+"','"+val+"')");        
    }

}

function setValue(e,i,o,v){

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

function showPoint(tab) {
    var point = tab.getAttribute('id');
    window.location = "index.php?cp="+point;
}


function showData(img){

    var uri = "load_data.php?id="+img.id+"&cp="+cp();
    var dataBox = document.getElementById("data_box");
    var trigerBox = document.getElementById("triger_box");
    
            
    ajax('GET',uri,function(){trigerBox.setAttribute("style", "display:none;"); dataBox.setAttribute("style", "display:block;"); dataBox.innerHTML=this;}); 
}

function changeLimit (id) {
    
    var lpm = document.getElementById('lpm');
    var lph = document.getElementById('lph');
    var uri = "forms/change_limit.php?cp="+cp()+"&lpm="+lpm.value+"&lph="+lph.value+"&id="+id;

    ajax('GET',uri,function(){document.getElementById('ch_lm').innerHTML=this;});

}

function changeMask (id,button) {
    
    var mask="";
    var inputArray = button.parentNode.getElementsByTagName('input');
    
    for (var i=0;i<inputArray.length-1;i++){

        if (inputArray[i].checked){
            mask=mask.concat('1');
        }
        else {
            mask=mask.concat('0');    
        }
    }
    
    mask=mask.concat('1');  

    var uri = "forms/change_mask.php?cp="+cp()+"&mask="+mask+"&id="+id;
    ajax('GET',uri,function(){document.getElementById('ch_ms').innerHTML=this;});

}


function changeSet (button) {
    
    var mask="";
    var inputArray = button.parentNode.getElementsByTagName('input');

    for (var i=0;i<inputArray.length;i++){

        if (inputArray[i].checked){
            mask=mask.concat('1');
        }
        else {
            mask=mask.concat('0');    
        }
    }

    var allMuxArray= document.getElementsByClassName('mux');
    var openMuxArray=[];
    var img;

    for (var z=0;z<allMuxArray.length;z++){
        var img = allMuxArray[z].getElementsByClassName("show_button");

        if(img[0].src.match(/minus_small.png/)) {
            openMuxArray.push(img[0]);
        }
    }  
    
    var uri = "base/set.php?mask="+mask;
    ajax('GET',uri,function(){for (var p=0;p<openMuxArray.length;p++){hideMux(openMuxArray[p]); showMux(openMuxArray[p]);} });

       

}

function alarm (alarm_div){
 document.getElementById('monitor').innerHTML=alarm_div;
}

function addChannel(button){
    
    var div,mid,uri,x;
    div = button.parentNode;

    while (div.className!='mux'){
        div=div.parentNode;      
    }
    
    mid=div.id;
    uri='base/add_channel.php?mid='+mid+'&cp='+cp();
    x = div.getElementsByClassName("show_button");

    ajax('GET',uri,function(){hideMux(x[0]),showMux(x[0])});
}


function delChannel(cid,delButton){
    
    var div,uri,x;
    div = delButton.parentNode;

    while (div.className!='mux'){
        div=div.parentNode;      
    }

    uri='base/del_channel.php?cid='+cid+'&cp='+cp();
    x = div.getElementsByClassName("show_button");

    ajax('GET',uri,function(){hideMux(x[0]),showMux(x[0])});
}

function access (select) {
    if(select.value=='1'){
        document.getElementById('user_point_select').disabled=0;
    }
    else {
        document.getElementById('user_point_select').disabled=1;
    }
}

function clearCPH () {

    var uri = 'base/clear_cph.php?cp='+cp(); 
    ajax('GET',uri,function(){document.getElementById('triger_box').innerHTML = ''});
    
}

var intervalArray=[];

window.onload = function () { 

    if (cp()=='alarm'){
        ajax('GET',"alarm.php",function(){alarm(this)});
        setInterval(function(){ajax('GET',"alarm.php",function(){alarm(this)});},10000);   
    }
    else {
        ajax('GET',"point_status.php?cp="+cp(),function(){status(this)});
        setInterval(function(){ajax('GET',"point_status.php?cp="+cp(),function(){status(this)});},10000);        
    }

}
