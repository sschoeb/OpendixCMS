//
// ELement an dem die neu abgefragten Daten angehängt werden
// z.B. wenn ein Subordner abgefragt wird, wird als elemtoappend
// das Element des subordners gespeichert, da der Inhalt des Ordners
// ja an den Subordner angehängt werden soll
//
var elemtoappend = null;

//
// Status ob der File-Browser angezeigt wird oder nicht
//
var browserstate = false;

//
// Name der Funktion die aufgerufen wird, wenn der User auf eine Datei klickt
//
var functname = '';

//
// Sendet eine Abfrage an den Webserver
//
function Request(folder)
{
	
	var link = CMS_URL + '';
	var sub = GetUrlParam("sub");
	var fbbaction = "action=ajaxfbb";
	if(sub != false)
	{
		link = link + "?sub=" + sub + "&" +fbbaction;
	}
	else
	{
		link = link +"?" +fbbaction;
	}
	
	if(folder != "")
	{
		link = link + '&folder=' + folder;
	}

	new Ajax.Request(link,
  	{
    	method:'get',
	    onSuccess: function(transport)
	    {
      		Answer(transport);
    	},
    	onFailure: function()
    	{ 
    		alert('Ein fehler ist aufgereten. Vorgang abgerbochen!') 
    	}
  	});
}

//
// Verarbeitet die Antwort des Webservers
//
function Answer(transport)
{

	var xml = transport.responseXML.documentElement.childNodes;
	var files = new Array();
	
	for(var i=0;i<xml.length;i++)
	{	
	 	var element = xml[i];

		if(element.nodeType == 1)
	 	{
	 		var isFile = false;
	 		var li = document.createElement("li");
	 		var value = Base64.decode(element.firstChild.nodeValue);
	 	 	if(element.getAttribute('type')=='folder')
	 	 	{
	 	 		
	 	 		if(value == "..")
	 	 		{
	 	 				//li.innerHTML = '<a href="javascript:JoinFolder(\'' + element.firstChild.nodeValue + '\', \''+ elemtoappend.id +'\');">' + element.firstChild.nodeValue + '</a>';
	 	 		}
	 	 		else
	 	 		{
	 	 			li.innerHTML = '<a href="javascript:JoinFolder(\'' + value + '\', \''+ value.sub('/', '',1000) +'\');"><img src="filebase/images/dir.png"/> ' + value + '</a>';
	 	 		}
	 	 	}
	 	 	else
	 	 	{
	 	 		isFile = true;
	 	 		li.innerHTML = '<a href="javascript:'+ funcname +'(\'' + value + '\');"><img src="filebase/images/file.gif" /> ' + value + '</a>';
	 	 	}
	 	 	
	 	 	li.id = value.sub('/', '',1000);

	 	 	if(isFile == true)
	 	 	{
	 	 		files.push(li);
	 	 		continue;
	 	 	}
	 	 	if(elemtoappend == null)
	 	 	{
	 	 		$("fblist").appendChild(li);	
	 	 	}
	 	 	else
	 	 	{
	 	 		$("ul" + elemtoappend.id).appendChild(li);
	 	 	}	 	 	
	 	}
	}
	
	for(var i=0;i<files.length;i++)
	{
	 	if(elemtoappend == null)
	 	{
	 		$("fblist").appendChild(files[i]);	
	 	}
	 	else
	 	{
	 		$("ul" + elemtoappend.id).appendChild(files[i]);
	 	}		
	}

	//Entfernen der "loading..."-nachricht
	$("fbbloadelem").remove();
}

//
// Wird aufgerufen wenn der Besucher auf einen zu öffnenden Ordner klickt
//
function JoinFolder(foldername, id)
{
	//Prüfen ob einfach eine Ebene nach oben verschoben werden soll
	if(foldername == '..')
	{
		$("ul" + id).remove();
		return;
	}
	
	
	var elem = $(id);

	for(var i=0;i<elem.childNodes.length;i++)
	{
		if(elem.childNodes[i].tagName == "UL")
		{
			$("ul" + id).remove();
			return;
		}
	}
	
	var ul = document.createElement("ul");
	ul.id = "ul" + id;
	$(id).appendChild(ul);
	elemtoappend = $(id);
	
	//Hinzufügen einer "loading..."-Nachricht
	var loadElem = document.createElement("li");
	loadElem.innerHTML = "loading...";
	loadElem.id ="fbbloadelem";
	$("ul" + id).appendChild(loadElem);
	
	//Abfrage an den Server senden
	Request(foldername);
}

//
// Zeigt den FilebaseBrowser an
//
function EnableBrowser()
{
	browserstate = true;
	if(($("fblist").childNodes.length == 1) || window.ActiveXObject != null)
	{
		Request('');
	}
	$('fbbbrowser').value = "Browser ausblenden";
	$('fbbrowser').show();
}

//
// Versteckt den Filebasebrowser
//
function DisableBrowser()
{
	browserstate = false;
	$('fbbrowser').hide();
	$('fbbbrowser').value = "Browser einblenden";
}

//
// Wechselt den Status des Filebase-Browsers
// Optional kann als Parameter der Funktionsname mitgegeben werden
// der aufgerufen wird, wenn auf eine Datei geklickt wird
//
function SwitchBrowser(func)
{
	if(func != null)
	{
		SetFunctionToCall(func);
	}
	if(browserstate)
	{
		DisableBrowser();
	}
	else
	{
		EnableBrowser();
	}
}

//
// Setzt die Funktion die aufgerufen wird wenn der Besucher auf eine Datei klickt
//
function SetFunctionToCall(param)
{
	funcname = param;
}