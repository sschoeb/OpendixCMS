
//
// Startet alle Uebergebenen Funktionen
// @param 	Array	funktionen	Alle Namen der Funktionen die ausgef�hrt werden sollen
//
function run(funktionen)
{
	// Jede Funktion durchgehen...
	for(var i = 0; i < funktionen.length; i++)
	{
		// .. und aufrufen!
		if(funktionen[i] == "empty")
		{
			return;
		}
		eval(funktionen[i] + ";");
	}
}

//
// Da es in JS kein Trim gibt -> hier die implementierung
//
function trim (zeichenkette) 
{
  // Erst führende, dann Abschließende Whitespaces entfernen
  // und das Ergebnis dieser Operationen zurückliefern
  return zeichenkette.replace(/^\s+/, '').replace(/\s+$/, '');
}

//
// Gibt einen in der URL übergebenen Parameter zurück
// @param String name Name des Parameters der abgefragt werden soll
//
function GetUrlParam(name)
{
	var paramstring = window.location.search;
	var params = paramstring.split("&");
	for(var i=0;i<params.length;i++)
	{
		var paramitem = params[i].split("=");
		if(paramitem[0] == name || paramitem[0] == ("?" + name))
		{
			return paramitem[1];
		}
	}
	return false;
}


//
// Fügt eine entsprechende Antwort auf eine AJAX-Anfrage in einen
// Select ein.
// @param XMLData answer XML, welches als ANtwort gekommen ist
// @param String selectName Name des Select-Feldes im Formular
// @param String nodeName Name des Attributs in dem der Name der Option steht.
//
function AddAnswerToSelect(answer, selectName, nodeName)
{
	
	var xml = answer.responseXML.documentElement.childNodes;
	for(var i=0;i<xml.length;i++)
	{	
	 	var element = xml[i];

		if(element.nodeType == 1)
	 	{
	 		var option = document.createElement('option');
	 		option.value = element.getAttribute('id');
	 		option.textContent = element.getAttribute(nodeName);
	 		var id = $(selectName).appendChild(option);
	 	}
	}	
}

//
// Analysiert eine XML-AJAX-Antwort. Es wird geprüft
// ob ein Fehler vorgekommen ist. Wenn nicht wird
// die als zweiter Parameter übergebene Funktion mit den
// XML-Daten ausgeführt.
// Sollte ein Fehler vorgekomen sein wird false zurückgegeben
// und dine entsprechend Fehlermeldung ausgegeben.
//
// @param Object answer Antwort die wir durch AJAX erhalten
//
function AnalyseAjaxAnswer(answer)
{
	var xml = answer.responseXML.documentElement.childNodes;
	
	if(xml.length != 0 && xml[0]["parentNode"]["nodeName"] == "error")
	{
		alert(xml[0].firstChild.nodeValue);
		return false;
	}
	return true;
	
}

//
// Gibt die URL mitsamt dem angehängten sub-Parameter zurück
//
function GetLinkWithSub()
{
	return CMS_URL + '?sub=' + GetUrlParam('sub');
}

//
// Gibt true or false zurück ob eine Liste über Listen-Elemente verfügt
// Es kann nicht das length-Attribut der Liste verwendet werden, da da
// auch die Eigenschaften der Liste (id, name, ...) als childNodes aufgeführt
// werden
//
function ListHasItems(list)
{
	for(i=0; i<list.childNodes.length; i++)
	{
		if(list.childNodes[i].nodeType == 1)
		{
			return true;
		}
	}
	return false;
}


//
// Gibt aus einem Smarty html_select_date/html_select_time einen Timestampf
// zur�ck
//
function GetSmartyTimestamp(fieldPrefix)
{
	var year = GetSelectedItemValue($(fieldPrefix + "[Year]"));
	var month = GetSelectedItemValue($(fieldPrefix + "[Month]"));
	var day = GetSelectedItemValue($(fieldPrefix + "[Day]"));
	var hour = GetSelectedItemValue($(fieldPrefix + "[Hour]"));
	var minute = GetSelectedItemValue($(fieldPrefix + "[Minute]"));
	// var second = $(fieldPrefix + "[Second]").Value;
	var second = 0;
	
	var date = new Date(year, month, day, hour, minute, second);
	return date.getTime();
}

function GetSmartyDate(fieldPrefix)
{
	var year = GetSelectedItemtext($(fieldPrefix + "[Year]"));
	var month = GetSelectedItemValue($(fieldPrefix + "[Month]"));
	var day = GetSelectedItemtext($(fieldPrefix + "[Day]"));
	var hour = GetSelectedItemtext($(fieldPrefix + "[Hour]"));
	var minute = GetSelectedItemtext($(fieldPrefix + "[Minute]"));
	
	return day + "." + month +  "." + year + " " + hour + ":" + minute;
}

function GetSelectedItemValue(list)
{
	for(i=0; i<list.childNodes.length; i++)
	{
		if(list.childNodes[i].selected)
		{
			return  list.childNodes[i].value;
		}
	}
	return null;
}

function GetSelectedItemtext(list)
{
	for(i=0; i<list.childNodes.length; i++)
	{
		if(list.childNodes[i].selected)
		{
			return  list.childNodes[i].innerText;
		}
	}
	return null;
}

function AddParamToUrl(url, key, value)
{
	if(url.indexOf('?', 0) == -1)
	{
		return url + '?' + key + '=' + value;
	}
	return url + '&' + key + '=' + value;
}

function ShowPopup (url, width, height) {
	   fenster = window.open(url, "Fenster", "width="+width+",height="+height+",status=yes,scrollbars=yes,resizable=yes");
	   fenster.focus();
	}

var Base64 = {

		// private property
		_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

		// public method for encoding
		encode : function (input) {
		    var output = "";
		    var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		    var i = 0;

		    input = Base64._utf8_encode(input);

		    while (i < input.length) {

		        chr1 = input.charCodeAt(i++);
		        chr2 = input.charCodeAt(i++);
		        chr3 = input.charCodeAt(i++);

		        enc1 = chr1 >> 2;
		        enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
		        enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
		        enc4 = chr3 & 63;

		        if (isNaN(chr2)) {
		            enc3 = enc4 = 64;
		        } else if (isNaN(chr3)) {
		            enc4 = 64;
		        }

		        output = output +
		        this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
		        this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

		    }

		    return output;
		},

		// public method for decoding
		decode : function (input) {
		    var output = "";
		    var chr1, chr2, chr3;
		    var enc1, enc2, enc3, enc4;
		    var i = 0;

		    input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

		    while (i < input.length) {

		        enc1 = this._keyStr.indexOf(input.charAt(i++));
		        enc2 = this._keyStr.indexOf(input.charAt(i++));
		        enc3 = this._keyStr.indexOf(input.charAt(i++));
		        enc4 = this._keyStr.indexOf(input.charAt(i++));

		        chr1 = (enc1 << 2) | (enc2 >> 4);
		        chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
		        chr3 = ((enc3 & 3) << 6) | enc4;

		        output = output + String.fromCharCode(chr1);

		        if (enc3 != 64) {
		            output = output + String.fromCharCode(chr2);
		        }
		        if (enc4 != 64) {
		            output = output + String.fromCharCode(chr3);
		        }

		    }

		    output = Base64._utf8_decode(output);

		    return output;

		},

		// private method for UTF-8 encoding
		_utf8_encode : function (string) {
		    string = string.replace(/\r\n/g,"\n");
		    var utftext = "";

		    for (var n = 0; n < string.length; n++) {

		        var c = string.charCodeAt(n);

		        if (c < 128) {
		            utftext += String.fromCharCode(c);
		        }
		        else if((c > 127) && (c < 2048)) {
		            utftext += String.fromCharCode((c >> 6) | 192);
		            utftext += String.fromCharCode((c & 63) | 128);
		        }
		        else {
		            utftext += String.fromCharCode((c >> 12) | 224);
		            utftext += String.fromCharCode(((c >> 6) & 63) | 128);
		            utftext += String.fromCharCode((c & 63) | 128);
		        }

		    }

		    return utftext;
		},

		// private method for UTF-8 decoding
		_utf8_decode : function (utftext) {
		    var string = "";
		    var i = 0;
		    var c = c1 = c2 = 0;

		    while ( i < utftext.length ) {

		        c = utftext.charCodeAt(i);

		        if (c < 128) {
		            string += String.fromCharCode(c);
		            i++;
		        }
		        else if((c > 191) && (c < 224)) {
		            c2 = utftext.charCodeAt(i+1);
		            string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
		            i += 2;
		        }
		        else {
		            c2 = utftext.charCodeAt(i+1);
		            c3 = utftext.charCodeAt(i+2);
		            string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
		            i += 3;
		        }

		    }

		    return string;
		}

		}
