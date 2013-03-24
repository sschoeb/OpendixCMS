/*******************************************************************************
 * Javascript fuer twGmap01einfach
 ******************************************************************************/

var elementId = "twGmap"; // muss im html an ein <div> als id vergeben werden
var lat = "47.180824"; // Breitengrad (Latitude) z.B: 51.041
var lon = "9.456096"; // 
var zoom = 15; // einen Wert von 1 bis ca 17 (nicht in "" setzen)

function twGmapLoad() {
	if (GBrowserIsCompatible()) {
		if (!document.getElementById(elementId)) {
			return false;
		}

		var map = new GMap2(document.getElementById(elementId), {
			size : new GSize(600, 450)
		});
		map.addControl(new GSmallMapControl());

		map.setCenter(new GLatLng(lat, lon), zoom, G_HYBRID_MAP);
		var point = new GLatLng(lat, lon);
		var marker = new GMarker(point);
		map.addOverlay(marker);
	}
}
