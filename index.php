<?php

session_start ();



//Für die Entwicklugn alle Error/Warnings/notice anzeigen
error_reporting ( E_ALL );

//Library-Loader einbinden
include_once ('lib/loader.php');

include_once 'configSelector.php';

try
{
	$cms = Cms::GetInstance ();
	$cms->Init ();
	$cms->LoadModule ();
	$cms->Output ();

} catch ( CMSException $ex )
{
	?>

<html>

<head>
<style type="text/css">
html {
	font-family: Verdana, Geneva, 'Lucida Sans Unicode', arial, Helvetica,
		sans-serif;
}

#errordiv {
	border: 2px solid red;
	background-color: #F82;
	padding: 10px;
}

#errordiv h1 {
	
}
</style>

</head>

<body>
<div id="errordiv">
<h1>Es ist ein Fehler aufgetreten</h1>
Leider kann die Seite nicht angezeigt werden. <br />
Sollte dieser Fehler erneut auftreten kontaktieren Sie uns bitte unter
folgender Adresse: <a href="mailto:error@tc-grabs.ch">error@tc-grabs.ch</a><br />
<br />
<b>Bitte f�gen Sie dem Mail die folgenden Informationen hinzu:</b>
<ul>
	<li>Beschreibung was genau Sie auf der Webseite durchgef�hrt haben</li>
	<li>Beschreibung was Sie erwaret haben</li>
	<li>Die URL/Link die Sie aufgerufen haben</li>
	<li>Den unterhalb stehenden Code zur Fehlererkennung</li>
</ul>
<b>Code zur Fehlererkennung:</b> <br />




<?php
	
	//Wenn hier ein Fehler abgefangen wird, dann fehlt irgendeine wichtige Datei, ansonsten würde
	//das CMS den Fehler spätestens auf Stufe einer oben aufgerufenen Methode abfangen und die entsprechende 
	//Fehlerbehandlung ausführen. 
	$str = '<h1>Es ist ein schwerer unterwarteter Fehler aufgetreten</h1>';
	$str .= $ex->getMessage ();
	$str .= "<br/>";
	$str .= "<b>" . $ex->getFile () . ' Line: ' . $ex->getLine () . "</b>";
	$str .= "<pre>";
	$str .= print_r ( $ex->getTrace (), true );
	$str .= "</pre>";
	$str .= '<hr/>';
	if (! is_null ( $ex->GetInnerException () ))
	{
		$str .= "<pre>";
		$str .= print_r ( $ex->GetInnerException (), true );
		$str .= "</pre>";
	}
	$str . "<br />";
	
	$enc = base64_encode ( $str );
	echo wordwrap ( $enc, 80, "<br />", true );
	
	?>
	
	</div>
</body>

</html><?php
	
	die ();
}