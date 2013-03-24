//
// Wird ausgefuehrt wenn die RadioBox geaendert wird
//
function radioBoxChange()
{
	var rb = document.getElementsByName('news[link][type]');
	if(rb[0].checked)
	{
		disableIntern();
		disableExtern();
		enableFilebase();
	}
	else if(rb[1].checked)
	{
		disableIntern();
		enableExtern();
		disableFilebase();
	}
	else if(rb[2].checked)
	{
		enableIntern();
		disableExtern();
		disableFilebase();
	}
}

function StartUp()
{
	disableIntern();
	disableExtern();
	disableFilebase();
	$('connmenue').selectedIndex=0;
	CheckLinkListVisible();
}

function disableIntern()
{
	$('linkInternal').style.display = 'none';
}

function enableIntern()
{
	$('linkInternal').style.display = '';
	if($("connconn").length == 0)
	{
		$("sconnconn").style.display="none";
	}
	if($("connelement").length==0)
	{
		$("sconnelement").style.display="none";
	}
}

function disableExtern()
{
	$('linkWebsite').style.display = 'none';
}

function enableExtern()
{
	$('linkWebsite').style.display = '';
}

function enableFilebase()
{
	$('linkFile').style.display = '';	
}

function disableFilebase()
{
	$('linkFile').style.display = 'none';
}

function connMenueChange()
{
	var id=$("connmenue").value;
	var link = CMS_URL + '?sub=' + GetUrlParam('sub') + '&action=ajaxconnmenue&id='+ id;

	new Ajax.Request(link,
  	{
    	method:'get',
	    onSuccess: function(transport)
	    {
      		if(AnalyseAjaxAnswer(transport, "connMenueChangeAnswer"))
      		{
      			connMenueChangeAnswer(transport);
      		}
    	},
    	onFailure: function()
    	{ 
    		alert('Ein fehler ist aufgereten. Vorgang abgerbochen!') 
    	}
  	});
}


function connMenueChangeAnswer(answer)
{

	$("sconnconn").style.display="";
	$("sconnelement").style.display="none";
	clearConnConn();
	clearConnElement();
	
	AddAnswerToSelect(answer, 'connconn', 'name');

	if($("connconn").length == 1)
	{
		$("sconnconn").style.display="none";
		connConnChange();
	}
	
	
	if($("connconn").length == 0)
	{
		
		$("sconnconn").style.display="none";
		return;
	}
	
	connConnChange();
}

//
// Wird aufgerufen, sobald der Benutzer den Wert des Connection-Selects ändert
//
function connConnChange()
{
	var mid=$("connmenue").value;
	var cid=$("connconn").value;
	var link = CMS_URL + '?sub=' + GetUrlParam('sub') + '&action=ajaxconnconn&cid='+ cid + '&mid=' + mid;

	new Ajax.Request(link,
  	{
    	method:'get',
	    onSuccess: function(transport)
	    {
      		connConnChangeAnswer(transport);
    	},
    	onFailure: function()
    	{ 
    		alert('Ein fehler ist aufgereten. Vorgang abgerbochen!') 
    	}
  	});
}

//
// Verarbeitet die Antwort des Webservers auf einen Connection-Wechsel
// Sind zu der gewählten Verbindung keine Elemente vorhanden, wird der 
// Select für die ELemente ausgeblendet.
//
// @param	XML-Antwort des Webservers
//			Es werden sämtliche Elemente für die gewählte Verbindung zurückgegeben
//
function connConnChangeAnswer(answer)
{
	$("sconnelement").style.display="";
	clearConnElement();
	AddAnswerToSelect(answer, 'connelement', 'name');
	
	if($("connelement").length == 0)
	{
		$("sconnelement").style.display="none";
	}
	
}

//
// Entfernt sämtliche Elemente aus dem Connection-Select
//
function clearConnConn()
{
	$("connconn").innerHTML = "";
}

//
// Entfernt sämtliche Elemente aus dem Element-Select
//
function clearConnElement()
{
	$("connelement").innerHTML = "";
}

//
// CallBack-Methode des FileBrowsers
// Wird ausgeführt, wenn der Benutzer eine Datei im FileBrowser gewählt hat
//
function takeNewsFile(fileName)
{
	$("filebaseFile").value=fileName;
}

//
// Wird ausgeführt, wenn der Benutzer auf den Butten "Link hinzufügen" klickt
//
function AddNewLink()
{
	var rb = document.getElementsByName('news[link][type]');
	var params = 'name=newLink';
	if(rb[0].checked)
	{
		params = params + '&linktype=FILEBASE&file=' + $('filebaseFile').value;
		$('filebaseFile').value = '';
	}
	else if(rb[1].checked)
	{
		var website = Base64.encode($('linkWebsiteInput').value);
		params = params + '&linktype=EXTERNAL&website=' + website;
		$('linkWebsiteInput').value = '';
	}
	else if(rb[2].checked)
	{
		params = params + '&linktype=INTERNAL&menueId=' + $('connmenue').value;
		
		if($('connconn') != null)
		{
			params = params +'&connectionId=' + $('connconn').value;
			if($('connelement') != null)
			{
				params = params + '&elementId=' + $('connelement').value;
			}
		}
	}
	
	var link = GetLinkWithSub() + '&id='+ GetUrlParam('id') +'&action=ajaxaddlink&' + params;

	alert(link);
	
	new Ajax.Request(link,
  	{
	    onSuccess: function(transport)
	    {
      		if(AnalyseAjaxAnswer(transport))
      		{
      			AddNewLinkAnswer(transport);
      		}
    	},
    	onFailure: function()
    	{ 
    		alert('Ein fehler ist aufgereten. Vorgang abgerbochen!') ;
    	}
  	});
}

//
// Bearbeitet die Antwort auf einen Add-Link-Anfrage
//
// @param	XML-Antowrt des Webservers. Enthält folgende Eigenschaften 
//			über den hinzugefügten Link:
//			- link 	-> Der Link der generiert wurde
//			- name	-> Name des Links
//			- id	-> ID des neuen Links
//			- type	-> LinkType -> INTERNAL, EXTERNAL, FILEBASE
//
function AddNewLinkAnswer(answer)
{
	$('linklistcontainer').style.display = '';
	$('linkname').value 		= '';
	$('linkWebsite').value 		= '';
	$('filebaseFile').value		= '';
	$('connmenue').selectedIndex= 0;
	$('connconn').innerHTML		= '';
	$('connelement').innerHTML	= '';

	var rb = document.getElementsByName('news[link][type]');
	rb[0].checked = false;
	rb[1].checked = false;
	rb[2].checked = false;
	
	disableIntern();
	disableExtern();
	disableFilebase();
		
	var xml = answer.responseXML.documentElement.childNodes;
	
	var li = document.createElement("li");
	//li.innerHTML = xml[0].getAttribute('name');
	
	var link = xml[0].getAttribute('link');
	var name = xml[0].getAttribute('name');
	var id = xml[0].getAttribute('id');
	var type = xml[0].getAttribute('type');
	var target = '';
	if(type != 'FILEBASE')
	{
		target =  "target=\"_blank\"";
	}
	
	li.innerHTML = "<a href=\""+ link +"\" "+ target +">"+ name +"</a> | <a href=\"javascript:DeleteLinkClick("+ id +");\">L&ouml;schen</a>"
	li.id="linkId" + id;
	
	$('avaibleLinks').appendChild(li);
	
}

//
// Wird ausgeführt, wenn der Benutzer einen Link entfernen will
//
// @param	ID des Links der gelöscht werden soll
//
function DeleteLinkClick(linkId)
{
	var link = GetLinkWithSub() + '&action=ajaxdeletelink&linkId='+ linkId;

	new Ajax.Request(link,
  	{

  		onSuccess: function(transport)
	    {
      		if(AnalyseAjaxAnswer(transport))
      		{
      			DeleteLinkAnswer(transport);
      		}
    	},
    	onFailure: function()
    	{ 
    		alert('Ein fehler ist aufgereten. Vorgang abgerbochen!') 
    	}
  	});
}

//
// Bearbeitet die Antwort auf eine Link-Löschen-Anfrage
//
// @param 	XML-Antwort des Webservers welche die Link_id
//			des gelöschten Links enthält
//
function DeleteLinkAnswer(answer)
{
	var xml = answer.responseXML.documentElement.childNodes;
	var li = $('linkId' + xml[0].attributes[0].nodeValue);
	li.remove();
	
	//Check if there are still Item's in the link-List
	CheckLinkListVisible();
}

//
// Prüft ob die Liste mit den vorhandenen Links angezeigt werden 
// muss oder nicht. Wird nur angezeigt wenn Links vorhanden
//
function CheckLinkListVisible()
{
	if(!ListHasItems($('avaibleLinks')))
	{
		$('linklistcontainer').style.display = 'none';
	}	
}
