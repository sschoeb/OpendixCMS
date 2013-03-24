//
// Status des FileBrowsers (angezeigt oder nicht angezeigt)
//
var state = 0;

//
// Anzahl hinzugefügter Files
//
var newFileCount = 0;

//
// Wechselt den Browser zwischen angezeigt und nicht angezeigt
//
function switchFileBrowser()
{

	if(state == 0){
		enableBrowser();
	}else{
		disableBrowser();
	}
}

//
// Zeigt den FileBrowser an
//
function enableBrowser()
{
	document.getElementById('fileBrowserButton').value = 'Filebrowser ausblenden';
	CreateFileBrowser('', '');
	state = 1;
}

//
// Blendet den Filebrowser aus
//
function disableBrowser()
{
	document.getElementById('fileBrowserButton').value = 'Filebrowser anzeigen';
	document.getElementById('fileTest').innerHTML = '';
	state=0;
}


//
// Wird ausgeführt wenn ein File ausgewäht wird
//
function takeFile(path)
{
	document.getElementById('newFileRow').style.display = 'block';
	document.getElementById('newFileCol').innerHTML = document.getElementById('newFileCol').innerHTML + '<input type="text" size="55" value="' + path +'" name="newAnhang[' + newFileCount + ']" id="newAnhang[' + newFileCount + ']" readonly><br />'
	newFileCount++;
}