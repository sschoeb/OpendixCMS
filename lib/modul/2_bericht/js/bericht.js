//
// Zähler für die Anhänge damit immer ein neuer Name generiert werden kann
//
var linkcount = 0;

//
// Funktion wird aufgerufen wenn der User eine Datei im fbbrowser selektiert
//
function linkgalery()
{
	var gallery = null;
	var galleryname = null;
	for (i=0;i<$('avaibleGallerie').length;i++) 
	{
    	if ($('avaibleGallerie').options[i].selected) 
    	{
       		gallery = $('avaibleGallerie').options[i].value;
       		galleryname = $('avaibleGallerie').options[i].textContent;
    	}
  	}
  	
  	
	$("linkedgalleries").style.display = "";

	//Prüfen ob diese Datei nicht bereits ausgewählt wurde
	var sfl = $("linkedList").childNodes.length;
	for(var i=0;i<sfl;i++)
	{
		var liNode = $("linkedList").childNodes[i];
		if(liNode.nodeType == 3)
		{
			continue;
		}
		var val = liNode.getElementsByTagName("input")[0].value;
		if(val == gallery)
		{
			alert("Dieser Gallerie wurde bereits verlinkt!");
			return;
		}
	}
	
	var li = document.createElement("li");
	li.id = "glink[" + linkcount + "]";
	
	var inp = document.createElement("input");
	inp.type = "hidden";
	inp.value = gallery;
	inp.name = "glink[" + linkcount + "]";
	inp.readOnly = true;
	
	var a = document.createElement("a");
	a.href="javascript:removelink('" +inp.name+"');";
	a.innerText = " Entfernen";		//IE
	a.textContent = " Entfernen";	//FF
	
	li.textContent = galleryname;
	
	li.appendChild(inp);
	li.appendChild(a);
	
	$("linkedList").appendChild(li);
	
	linkcount = linkcount + 1;
}

//
// Entfernt ein Element aus der Liste der Ausgewählten Anhänge
// @param String file 	Name des Elements welches entfernt werden soll
//
function removelink(file)
{
	$(file).remove();
	//Prüfen ob noch Elemente in der Liste sind
	//Im IE wird die ID der liste nicht als child notiert in FF und Opera schon!
	if(($("linkedList").childNodes.length == 1 && window.ActiveXObject == null)  || (window.ActiveXObject != null && $("linkedList").childNodes.length == 0))
	{
		//wenn nicht dann die Liste nicht mehr anzeigen 
		$("linkedgalleries").style.display = "none";
	}
}