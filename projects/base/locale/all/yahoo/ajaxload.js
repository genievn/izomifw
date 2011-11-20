if (typeof IZOMIPORTAL == "undefined") {var IZOMIPORTAL = {};}

IZOMIPORTAL.ajaxload = {};

IZOMIPORTAL.ajaxload.buttonText = {};
IZOMIPORTAL.ajaxload.source = {};
IZOMIPORTAL.ajaxload.result = {};
IZOMIPORTAL.ajaxload.event = new YAHOO.util.CustomEvent("ajaxload");

IZOMIPORTAL.ajaxload.init = function() {
    YAHOO.util.Event.on(window, 'load', IZOMIPORTAL.ajaxload.setSeoButtons, this, true);
};

IZOMIPORTAL.ajaxload.setSeoButtons = function() {
    buttons = document.getElementsByTagName('iz:ajaxload');
    for(var i=0;i<buttons.length;i++) {
        this.setSeoButton(buttons[i]);
    }
}

IZOMIPORTAL.ajaxload.setSeoButton = function(button) {
    element = document.createElement("a");
    element.setAttribute("href", "#ajaxload");
    element.setAttribute('onclick', 'IZOMIPORTAL.ajaxload.search("'+button.getAttribute('src')+'", "'+button.getAttribute('source')+'", "'+button.getAttribute('result')+'");');
    element.appendChild(document.createTextNode(button.getAttribute('value')));
    button.appendChild(element);
};

IZOMIPORTAL.ajaxload.insertData = function(o) {
    result = eval( '(' + o.responseText + ')' );
    
    if(result['result'].length>1) {    	
        this.callback.element.value = result['result'].join(', ');
    }else{
        this.callback.element.value = result['result'];
    }
};

IZOMIPORTAL.ajaxload.insertError = function(o) {
    this.callback.element.value = 'Error';
};

IZOMIPORTAL.ajaxload.search = function(url, source, result) {
    search = '';
    source = source.split(',');
    for(var i=0;i<source.length;i++) {
        search = search +' '+ document.getElementById(source[i]).value;
    }
    postData = 'query='+search;
    this.callback.element = document.getElementById(result);
    
    YAHOO.util.Connect.asyncRequest('POST', url, this.callback, postData);
};

IZOMIPORTAL.ajaxload.callback = {};
IZOMIPORTAL.ajaxload.callback.element = {};
IZOMIPORTAL.ajaxload.callback.scope = IZOMIPORTAL.ajaxload;
IZOMIPORTAL.ajaxload.callback.success = IZOMIPORTAL.ajaxload.insertData;
IZOMIPORTAL.ajaxload.callback.failure = IZOMIPORTAL.ajaxload.insertError;

IZOMIPORTAL.ajaxload.init();