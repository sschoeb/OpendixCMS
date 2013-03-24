

function addTimer()
{
	var id=$("addTimerAction").value;
	var date=GetSmartyTimestamp("addTimerDate");
	var link = CMS_URL + 'index.php?sub=' + GetUrlParam('sub') + '&entityId='+ GetUrlParam('id') +'&action=AJAXAddTimer&actionid='+ id + '&date='+date;

	
	new Ajax.Request(link,
  	{
    	method:'get',
	    onSuccess: function(transport)
	    {
		
      		if(AnalyseAjaxAnswer(transport))
      		{
      	
      			addTimerAnswer(transport);
      		}
    	},
    	onFailure: function()
    	{ 
    		alert('Ein fehler ist aufgereten. Vorgang abgerbochen!') 
    	}
  	});
}

function addTimerAnswer(answer)
{
	
	var xml = answer.responseXML.documentElement.childNodes;
	
	
	
	for(var i=0;i<xml.length;i++)
	{
		
	 	var element = xml[i];

	 	
	 	
		if(element.nodeType == 1)
	 	{
	 		addTimerItem(element.getAttribute('id'));
	 	}
	}
}

function removeTimer(id)
{
	var link = CMS_URL + 'index.php?sub=' + GetUrlParam('sub') + '&action=AJAXRemoveTimer&timerId='+ id;


	new Ajax.Request(link,
  	{
    	method:'get',
	    onSuccess: function(transport)
	    {
			
      		if(AnalyseAjaxAnswer(transport))
      		{
      			
      			removeTimerAnswer(transport);
      		}
    	},
    	onFailure: function()
    	{ 
    		alert('Ein fehler ist aufgereten. Vorgang abgerbochen!') ;
    	}
  	});
}

function removeTimerAnswer(answer)
{
	var xml = answer.responseXML.documentElement.childNodes;
	
	for(var i=0;i<xml.length;i++)
	{	
	 	var element = xml[i];

		if(element.nodeType == 1)
	 	{
			$("timeritem" + element.getAttribute('id')).remove();
	 		break;
	 	}
	}
	checkTimerBlockVisibility();
	
}

function addTimerItem(id)
{
	
	var trItem = document.createElement('tr');
	trItem.id = "timeritem" + id;
	
	var tdName = document.createElement('td');
	tdName.innerHTML = GetSelectedItemtext($("addTimerAction"));
	var tdDate =  document.createElement('td');
	tdDate.innerHTML = GetSmartyDate("addTimerDate");
	
	var tdDelete =  document.createElement('td');
	tdDelete.innerHTML = '<a href="javascript:removeTimer('+ id +')">L&ouml;schen</a>';
	
	trItem.appendChild(tdDate);
	trItem.appendChild(tdName);
	trItem.appendChild(tdDelete);
	
	$("timertable").appendChild(trItem);
	
	checkTimerBlockVisibility();
}

function checkTimerBlockVisibility()
{
	
	var vis = "";
	if($("timertable").childNodes.length == 1)
		vis = "none";
	
	$("timertabletitle").style.display=vis;
	$("timertable").style.display=vis;
}
