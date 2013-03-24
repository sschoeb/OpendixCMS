
/**
 * Boolean ob der gesamte Import bereits abgeschlossen ist
 */
var importDone = false;
var running = false;
var imgcount = 0;

/**
 * Id der Galerie in welche die Bilder importiert werden sollen
 */
var gid = 0;

function start(id, count)
{
	imgcount = count;
	$('impButton').value = "Import stoppen";
	
	if(running)
	{
		$('impButton').disabled = true;
		$('impButton').value = "Bitte warten";
		appendLi("Bilder-Import wird gestoppt");
		running = false;
		return;
	}
	
	appendLi("Bilder-Import gestartet...");
	running = true;
	startImport(id);
}

function finishImport()
{
	appendLi("Bilder-Import abgeschlossen");
	$('imagesAvailable').style.visible = 'none';
	$('imagesAvailable').innerHTML = 'Import wurde abgeschlossen.';
}


/**
 * Startet den Import der Bilder im Import-Ordner in die Uebergebene Galerie
 * 
 * @param id	Id der Galerie zu welcher die Bilder hinzugefügt werden
 */
function startImport(id)
{	
	if(!running)
	{
		appendLi("Bilder-Import gestoppt");
		$('impButton').disabled = false;
		$('impButton').value = "Import starten";
		return;
	}
	
	gid=id;
	
	importSingleFile(id);
}

/**
 * 
 * Importiert ein einzelnes File
 * 
 * @param id
 * @return
 */
function importSingleFile(id)
{
	var url = AddParamToUrl(CMS_URL, 'gid', gid);
	url = AddParamToUrl(url, 'action', 'AJAX_import');
	url = AddParamToUrl(url, 'sub', GetUrlParam('sub'));

	new Ajax.Request(url,
	{
	 	method:'get',
	    onSuccess: function(transport)
	    {
	     	importAnswer(transport);
	    },
	    onFailure: function(){ alert('Something went wrong...'); }
	});
}

/**
 * Bearbeitet die Antwort die von PHP kommt (AJAX)
 * 
 * @param transport
 * @return
 */
function importAnswer(transport)
{
	
	var theanswer = transport.responseText;
	if(theanswer.substring(0,1) == '1')
	{
		appendLi(theanswer.substring(1));
		setTimeout('startImport('+ gid +')', 300);
		imgcount = imgcount -1;
		$('imgCount').innerHTML = imgcount;
		return;
	}

	if(theanswer.substring(0,1) == '0')
	{
		appendLi('Fehler: ' +theanswer.substring(1));
		return;
	}

	if(theanswer.substring(0,1) == '3')
	{
		finishImport();
		return;
	}

	appendLi('Unbekannte Antwort: ' +theanswer.substring(1));
	setTimeout('startImport('+ gid +')', 300);
}

/**
 * Fuegt der Liste ein Element mit dem als Parameter Uebergebenen Text hinzu
 * 
 * @param text
 * @return
 */
function appendLi(text)
{
	var newLI = document.createElement("li");
  	var newLIText = document.createTextNode(text);
  	document.getElementById("impList").appendChild(newLI);
  	document.getElementById("impList").lastChild.appendChild(newLIText);
}