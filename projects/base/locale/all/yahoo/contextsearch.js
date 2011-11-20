if (typeof IZOMIPORTAL == "undefined") {var IZOMIPORTAL = {};}

IZOMIPORTAL.contextsearch = {};

IZOMIPORTAL.contextsearch.buttonText = {};
IZOMIPORTAL.contextsearch.source = {};
IZOMIPORTAL.contextsearch.result = {};

IZOMIPORTAL.contextsearch.init = function() {
    YAHOO.util.Event.on(window, 'load', IZOMIPORTAL.contextsearch.setSeoButtons, this, true);
};

IZOMIPORTAL.contextsearch.setSeoButtons = function() {	
    buttons = document.getElementsByTagName('iz:contextsearch');
    for(var i=0;i<buttons.length;i++) {
        this.setSeoButton(buttons[i]);
    }
}

IZOMIPORTAL.contextsearch.setSeoButton = function(button) {
    element = document.createElement("a");
    element.setAttribute("href", "#contextsearch");
    element.setAttribute('onclick', 'IZOMIPORTAL.contextsearch.search("'+button.getAttribute('src')+'", "'+button.getAttribute('source')+'", "'+button.getAttribute('result')+'");');
    element.appendChild(document.createTextNode(button.getAttribute('value')));
    button.appendChild(element);
};

IZOMIPORTAL.contextsearch.insertData = function(o) {
    result = eval( '(' + o.responseText + ')' );
    
    if(result['result'].length>1) {    	
        this.callback.element.value = result['result'].join(', ');
    }else{
        this.callback.element.value = result['result'];
    }
};

IZOMIPORTAL.contextsearch.insertError = function(o) {
    this.callback.element.value = 'Error';
};

IZOMIPORTAL.contextsearch.search = function(url, source, result) {
    search = '';
    source = source.split(',');
    for(var i=0;i<source.length;i++) {
        search = search +' '+ document.getElementById(source[i]).value;
    }
    postData = 'query='+search;
    this.callback.element = document.getElementById(result);
    
    YAHOO.util.Connect.asyncRequest('POST', url, this.callback, postData);
};

IZOMIPORTAL.contextsearch.callback = {};
IZOMIPORTAL.contextsearch.callback.element = {};
IZOMIPORTAL.contextsearch.callback.scope = IZOMIPORTAL.contextsearch;
IZOMIPORTAL.contextsearch.callback.success = IZOMIPORTAL.contextsearch.insertData;
IZOMIPORTAL.contextsearch.callback.failure = IZOMIPORTAL.contextsearch.insertError;

IZOMIPORTAL.contextsearch.init();