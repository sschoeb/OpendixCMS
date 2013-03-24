//
// Boolean ob der gesamte Import bereits abgeschlossen ist!
//
var importDone = false;
var gid = 0;
var url = window.location.href + '/lib/modul/4_galerie/ajax/galerie_addfile.php';

//
// Startet den Import der Bilder im Import-Ordner in die Uebergebene Galerie
//
function startImport(id)
{	
	gid = id;
	importSingleFile(id);
	
	if(importDone == true)
	{
		document.getElementById('btnImportFinish').style.display = '';
		document.getElementById('titleBox').innerText = 'Import abgeschlossen'
		return;
	}
}

//
// Ruft das PHP-File auf welches den Datei-Import macht
//
function importSingleFile(id)
{
	url = OPENDIX_URL + 'lib/modul/4_galerie/ajax/galerie_addfile.php';
	url = url + '?gId=' + id;
	//alert(url);
	/*g_ajax_obj.CallXMLHTTPObjectGETParam ( url, answer, '' );
	SimpleAJAXCall(url,answer)*/
	new Ajax.Request(url,
	{
	 	method:'get',
	    onSuccess: function(transport)
	    {
	     	answer(transport);
	    },
	    onFailure: function(){ alert('Something went wrong...') }
	});
}

//
// Bearbeitet die Antwort die von PHP kommt (AJAX)
//
function answer(transport)
{
	
	var theanswer = transport.responseText;
	if(theanswer.substring(0,1) == '1')
	{
		appendLi(theanswer.substring(1));
		setTimeout('startImport('+ gid +')', 1000);
		return;
	}

	if(theanswer.substring(0,1) == '0')
	{
		appendLi('Fehler: ' +theanswer.substring(1));
		setTimeout('startImport('+ gid +')', 1000);
		return;
	}

	if(theanswer.substring(0,1) == '3')
	{
		importDone = true;
		setTimeout('startImport('+ gid +')', 1000);
		return;
	}

	appendLi('Unbekannte Antwort: ' +theanswer.substring(1));
	setTimeout('startImport('+ gid +')', 1000);
}

//
// Fuegt der Liste ein Element mit dem als Parameter Uebergebenen Text hinzu
//
function appendLi(text)
{
	var newLI = document.createElement("li");
  	var newLIText = document.createTextNode(text);
  	document.getElementById("impList").appendChild(newLI);
  	document.getElementById("impList").lastChild.appendChild(newLIText);
}