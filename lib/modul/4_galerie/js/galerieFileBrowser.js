//
// Status des FileBrowsers (angezeigt oder nicht angezeigt)
//
var state = 0;

//
// Anzahl hinzugefügter Files
//
var newFileCount = 0;


//
// Wird ausgeführt wenn ein File ausgewäht wird
//
function takeGalerieFile(path)
{
	//document.getElementById('newFileCol').style.display = 'block';
	document.getElementById('filebrowserFile').value = path;
	//document.getElementById('filebrowserFileView').innerHTML = path;
	newFileCount++;
}

//
// Wechselt den Browser zwischen angezeigt und nicht angezeigt
//
function switchGalerieFileBrowser()
{
	
	if(state == 0){
		enableGalerieBrowser();
	}else{
		disableBrowser();
	}
}

//
// Zeigt den FileBrowser an
//
function enableGalerieBrowser()
{
	document.getElementById('fileBrowserButton').value = 'Filebrowser ausblenden';
	CreateFileBrowser('', 'takeGalerieFile');
	state = 1;
}