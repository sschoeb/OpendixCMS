
function GetCmsUrl()
{
	var location = window.location;
	var url = location.protocol + "//" + location.host + location.pathname;
	return url;
}

///
/// URL auf der die Seite läuft
///
var CMS_URL = GetCmsUrl();
//var CMS_URL = "http://tc-grabs.ch/";
