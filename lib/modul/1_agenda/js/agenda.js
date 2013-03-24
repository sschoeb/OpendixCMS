//
// Zähler für die Anhänge damit immer ein neuer Name generiert werden kann
//
var agendacount = 0;

//
// Funktion wird aufgerufen wenn der User eine Datei im fbbrowser selektiert
//
function takeAgendaFile(file)
{
	$("selectedFiles").style.display = "";
	
	/*var sfl = $("selectedFilesList").childNodes.length;
	for(var i=0;i<sfl;i++)
	{
		var liNode = $("selectedFilesList").childNodes[i];
		if(liNode.nodeType == 3)
		{
			continue;
		}
		var val = liNode.getElementsByTagName("input")[0].value;
		if(val == file)
		{
			alert("Sie haben diese Datei bereits als Anghang ausgewählt!");
			return;
		}
	}*/
	
	
	var tr = document.createElement("tr");
	tr.id = "anhang[" + agendacount + "]";
	
	var inp = document.createElement("input");
	inp.type = "hidden";
	inp.value = file;
	inp.name = "anhang[" + agendacount + "]";
	inp.readOnly = true;
	
	var tdname = document.createElement("td");
	tdname.innerHTML = file;
	
	var tdhidden = document.createElement("td");
	tdhidden.appendChild(inp);
	
	var tdRemove = document.createElement("td");
	tdRemove.innerHTML = '<a href="javascript:removefile(\''+ inp.name + '\');">Entfernen</a>';
	
	tr.appendChild(tdname);
	tr.appendChild(tdhidden);	
	tr.appendChild(tdRemove);
	
	
	$("selectedFilesTable").appendChild(tr);
	
	agendacount = agendacount + 1;
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
	
	if(($("selectedFilesTable").childNodes.length == 1 && window.ActiveXObject == null)  || (window.ActiveXObject != null && $("selectedFilesTable").childNodes.length == 0))
	{
		//wenn nicht dann die Liste nicht mehr anzeigen 
		$("selectedFiles").style.display = "none";
	}
}

//
// Wird aufgerufen, wenn der Benutzer den Select für die BerichtMenueId ändert
// Es müssen dann per AJAX sämtliche Berichte abgefragt werden die zum entsprechenden
// ID-Punkt gehören. Dazu hat die Klasse OpenBericht die action ajaxgetbericht über
// die man alle Berichte eines Menüpunkts im XML-Format erhalten kann
//
function ChangeBerichtMenueId()
{	
	if($("berichtmenueid").children[0].selected)
	{
		CheckBerichtOnBoot();
		return;
	}
	$("berichtid").innerHTML = "";
	$("berichtIdLabel").style.display="";
	
	var sub = $("berichtmenueid").value;

	if(sub == 0)
	{	
		$("berichtIdLabel").style.display="none";
		return;
	}

	var link = CMS_URL + 'index.php';
	
	var fbbaction = "action=ajaxgetbericht";

	
	link = link + "?sub=" + sub + "&" +fbbaction;


	
	new Ajax.Request(link,
  	{
    	method:'get',
	    onSuccess: function(transport)
	    {
      		ChangeBerichtMenueIdAnswer(transport);
    	},
    	onFailure: function()
    	{ 
    		alert('Ein fehler ist aufgereten. Vorgang abgerbochen!'); 
    	}
  	});
  	
}

function ChangeBerichtMenueIdAnswer(transport)
{
	var xml = transport.responseXML.documentElement.childNodes;

	for(var i=0;i<xml.length;i++)
	{	
	 	var element = xml[i];

		if(element.nodeType == 1)
	 	{
	 		var option = document.createElement("option");
	 		option.value = element.getAttribute('id');
	 		option.textContent = element.getAttribute('title');
	 		var id = $("berichtid").appendChild(option);
	 	}
	}
}

//
// Wird nach dem Laden der Seite aufgerufen 
// Sorgt dafür, dass die AUswahl für den Bericht
// nicht angezeigt wird, solange kein Bericht-Menüpunkt
// ausgewählt wurde
//
function CheckBerichtOnBoot()
{
	if($("berichtmenueid").children[0].selected)
	{
		$("berichtIdLabel").style.display="none";
	}
}