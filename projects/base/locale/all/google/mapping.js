if (typeof IZOMIPORTAL == "undefined") {var IZOMIPORTAL = {};}

IZOMIPORTAL.mapping = {};

IZOMIPORTAL.mapping.map = null;

IZOMIPORTAL.mapping.init = function(){
    YAHOO.util.Event.onAvailable("iz:mapping", IZOMIPORTAL.mapping.loadMap);
    YAHOO.util.Event.addListener(document.getElementsByName("iz:mapping"), "click", IZOMIPORTAL.mapping.panZoomMap);
};

IZOMIPORTAL.mapping.loadMap = function(e) {
    if (GBrowserIsCompatible()) {
        IZOMIPORTAL.mapping.map = new GMap2(this);
        IZOMIPORTAL.mapping.map.setCenter(
            new GLatLng(
                this.getAttribute("lat"), 
                this.getAttribute("lon")), 
                Number(this.getAttribute("zoom")));
        IZOMIPORTAL.mapping.loadMarkers();
        if(this.getAttribute("control")=="small")
            IZOMIPORTAL.mapping.map.addControl(new GSmallMapControl());
        if(this.getAttribute("control")=="large")
            IZOMIPORTAL.mapping.map.addControl(new GLargeMapControl());
        if(this.getAttribute("types")=="true")
            IZOMIPORTAL.mapping.map.addControl(new GMapTypeControl());
        GEvent.addListener(
            IZOMIPORTAL.mapping.map,
            "moveend",
            IZOMIPORTAL.mapping.setMapBounds);
        IZOMIPORTAL.mapping.container = this;
    }
}

IZOMIPORTAL.mapping.loadMarkers = function() {
    if(IZOMIPORTAL.mapping.map == null) return 0;
    markers = document.getElementsByTagName("iz:mapping");
    for(i=0,j=markers.length;i<j;i++){
        IZOMIPORTAL.mapping.addMarker(
            markers[i].getAttribute("lat"),
            markers[i].getAttribute("lon"),
            markers[i].getAttribute("alt"));
    }
}

IZOMIPORTAL.mapping.addMarker = function(lat, lon, id) {
    if(IZOMIPORTAL.mapping.map == null) return 0;
    var point = new GLatLng(lat, lon);
    IZOMIPORTAL.mapping.map.addOverlay(new GMarker(point));
}

IZOMIPORTAL.mapping.removeMarkers = function() {
    IZOMIPORTAL.mapping.map.clearOverlays();
};

IZOMIPORTAL.mapping.panZoomMap = function(e) {
    if(IZOMIPORTAL.mapping.map == null) return 0;
    IZOMIPORTAL.mapping.map.panTo(
        new GLatLng(
            this.getAttribute("lat"), 
            this.getAttribute("lon")));
}

IZOMIPORTAL.mapping.setMapBounds = function() {
    var bounds = IZOMIPORTAL.mapping.map.getBounds();
    IZOMIPORTAL.mapping.container.setAttribute("lat", bounds.getCenter().lat());
    IZOMIPORTAL.mapping.container.setAttribute("lon", bounds.getCenter().lng());
    IZOMIPORTAL.mapping.container.setAttribute("zoom", IZOMIPORTAL.mapping.map.getZoom());
    IZOMIPORTAL.mapping.container.setAttribute("nelat", bounds.getNorthEast().lat());
    IZOMIPORTAL.mapping.container.setAttribute("nelon", bounds.getNorthEast().lng());
    IZOMIPORTAL.mapping.container.setAttribute("swlat", bounds.getSouthWest().lat());
    IZOMIPORTAL.mapping.container.setAttribute("swlon", bounds.getSouthWest().lng());
}

IZOMIPORTAL.mapping.init();