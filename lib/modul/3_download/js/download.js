//
// Zähler für die Anhänge damit immer ein neuer Name generiert werden kann
//
var agendacount = 0;

//
// Zwischenspeicher für die Gruppen damit nicht jedes Mal der Server angefragt werden muss
//
var groupselectoptions = '';

//
// Wird aufgerufen wenn über den Filebrowser eine Datei zur Download-Verlinkung ausgewählt wird
//
function takeDownloadFile(file)
{
	$("selectedFiles").style.display = "";
	
	var li = document.createElement("li");
	li.id = "anhang[" + agendacount + "][path]";
	li.textContent = file;
	var inp = document.createElement("input");
	inp.type = "hidden";
	inp.value = file;
	inp.name = "anhang[" + agendacount + "][path]";
	inp.readOnly = true;
	
	
	var groupselect = document.createElement("select");
	groupselect.name = "anhang[" + agendacount + "][group]";
	groupselect.id = "anhang[" + agendacount + "][group]";
	li.appendChild(groupselect);
	
	
	var a = document.createElement("a");
	a.href="javascript:removefile('" +inp.name+"');";
	a.innerText = " Entfernen";		//IE
	a.textContent = " Entfernen";	//FF
	
	li.appendChild(inp);
	li.appendChild(a);
	
	$("selectedFilesList").appendChild(li);
	
	GetGroupSelectOptions("anhang[" + agendacount + "][group]");
	
	agendacount = agendacount + 1;
}

//
// Fügt dem select-Element im elemToUpdate die Gruppen-Options hinzu
// Beim ersten Mal wird der Server angefragt, anschliessend sind alle
// Options in der Variabel "groupselectoptions" zwischengespeichert und
// werden daraus geladen!
//
function GetGroupSelectOptions(elemToUpdate)
{
	//Prüfen ob die Optionen bereits vorhanden sind...
	if(groupselectoptions != '')
	{
		//.. wenn ja dann gleich diese laden
		LoadGroupOptions(elemToUpdate);
		return;
	}
	//.. ansonsten die Abfrage an den Server senden
	var link = CMS_URL;
	var sub = GetUrlParam("sub");
	var fbbaction = "action=ajaxgetgroup";
	if(sub != false)
	{
		link = link + "?sub=" + sub + "&" +fbbaction;
	}
	else
	{
		link = link +"?" +fbbaction;
	}

	new Ajax.Request(link,
  	{
    	method:'get',
	    onSuccess: function(transport)
	    {
      		GroupAnswer(transport, elemToUpdate);
    	},
    	onFailure: function()
    	{ 
    		alert('Ein fehler ist aufgereten. Vorgang abgerbochen!') 
    	}
  	});
}

//
// Antwort des Servers verarbeiten
// Alle erhaltenen Options werden in "groupselectoptions" und erst durch den aufruf
// von LoadGroupOptions(elemToUpdate) in das entsprechende Element geladen
//
function GroupAnswer(transport, elemToUpdate)
{
	var xml = transport.responseXML.documentElement.childNodes;
	for(var i=0;i<xml.length;i++)
	{

	 	var element = xml[i];
	
		if(element.nodeType == 1)
	 	{	
	 		groupselectoptions += "<option value=\""+ element.getAttribute('id') +"\">" + element.getAttribute('name') + "</option>";
	 	}
	}
	LoadGroupOptions(elemToUpdate);
}

//
// Laden der zwischengespeicherten Optionen in das Select-Element
//
function LoadGroupOptions(elemToUpdate)
{
	$(elemToUpdate).innerHTML = groupselectoptions;
}

//
// Entfernt ein Element aus der Liste der Ausgewählten Anhänge
// @param String file 	Name des Elements welches entfernt werden soll
//
function removefile(file)
{
	$(file).remove();
	//Prüfen ob noch Elemente in der Liste sind
	//Im IE wird die ID der liste nicht als child notiert in FF und Opera schon!
	if(($("selectedFilesList").childNodes.length == 1 && window.ActiveXObject == null)  || (window.ActiveXObject != null && $("selectedFilesList").childNodes.length == 0))
	{
		//wenn nicht dann die Liste nicht mehr anzeigen 
		$("selectedFiles").style.display = "none";
	}
}