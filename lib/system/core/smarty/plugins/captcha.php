<?php
$tempfolder = "../../../../../temp/captcha";

if (is_md5($_GET['cap'])){
	$png = $_GET['cap'];
	//header("Content-type: image/png");
	
	$fp = fopen("$tempfolder/$png.png", 'rb');
	fpassthru($fp);
	fclose($fp);
	unlink("$tempfolder/$png.png");
}



function is_md5($var) {
   if(ereg('^[A-Fa-f0-9]{32}$',$var)) {
     return 1;
   } else {
     return 0;
   }        
 }
?>