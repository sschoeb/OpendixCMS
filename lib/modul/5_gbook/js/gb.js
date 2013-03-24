//Funktionen um Textelemente in eine Textbox an der Cursor-Position einzuf�gen!
function insert(form, element, aTag, eTag) 
{
	var input = document.forms[form].elements[element];
	input.focus();
	if(typeof document.selection != 'undefined') 
	{
		var range = document.selection.createRange();
		var insText = range.text;
		range.text = aTag + insText + eTag;
		range = document.selection.createRange();
		if (insText.length == 0) 
		{
			range.move('character', -eTag.length);
		} 
		else 
		{
			range.moveStart('character', aTag.length + insText.length + eTag.length);
		}
		range.select();
	}
	else if(typeof input.selectionStart != 'undefined')
	{
		var start = input.selectionStart;
		var end = input.selectionEnd;
		var insText = input.value.substring(start, end);
		input.value = input.value.substr(0, start) + aTag + insText + eTag + input.value.substr(end);
		var pos;
		if (insText.length == 0) 
		{
			pos = start + aTag.length;
		} 
		else 
		{
			pos = start + aTag.length + insText.length + eTag.length;
		}
		input.selectionStart = pos;
		input.selectionEnd = pos;
	} 
	else 
	{
		var pos;
		var re = new RegExp('^[0-9]{0,3}$');
		while(!re.test(pos)) 
		{
			pos = prompt("Einfuegen an Position (0.." + input.value.length + "):", "0");
		}
		if(pos > input.value.length) 
		{
			pos = input.value.length;
		}
		var insText = prompt("Bitte geben Sie den zu formatierenden Text ein:");
		input.value = input.value.substr(0, pos) + aTag + insText + eTag + input.value.substr(pos);
	}
}

//
// Wird aufgerufen wenn der Benutzer in die Textarea klickt damit
// "Ihr Beitrag" automatishc entfernt wird
//
function enterTextField()
{
	return;
	if($('nachricht').value == 'Ihr Beitrag')
	{
		$('nachricht').value = '';
	}
}

function enterCaptchaField()
{
	return;
	if($('captcha').value == 'L&ouml;sung')
	{
		$('captcha').value = '';
	}
}