
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">

<head>
    <title>Tennis Club Grabs</title>
    <meta http-equiv="content-type" content="text/html;" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <!-- <link href="{$cms.template.path.css}/style.css" type="text/css" rel="stylesheet" /> -->
    {include file="cms_import.tpl" imgPath=$cms.path.template.image}
</head>



<body onload="run(new Array({foreach item=jas from=$cms.runatstart}'{$jas}',{/foreach}'empty'))">

    <div id="wrapper">
        <div id="header"></div> <!-- header end -->
        <div id="contentbody">
            <div id="menue">
               
               {include file="open_menue.tpl"}
               
            	<a href="http://www.sport-verein-t.ch/" target="_blank"><img class="bild" style="margin: 10px 0 10px 8px;" alt="Sport-Vereint Logo" src="{$cms.path.template.image}/sport-verein-t.jpg" /></a>           
            </div>
            <!-- menue end -->
            
            
            <div id="content">

			    
            	{include file="cms_content.tpl"}
            
            </div>
            <!-- content end -->
            
            <div class="clear"><br /></div>
            
            <div id="impressum">
                <a href="mailto:webmaster@tc-grabs.ch">Webmaster</a> |<a href="impressum.html">Impressum</a> | <a href="disclaimer.html">Disclaimer</a> | <a href="{$cms.link.login}">{if $cms.user.isdeclared == 1}Logout{else}Login{/if}</a> <!--|   <a href="http://www.macu-webdesign.de" target="_blank" title="Webdesign Uelzen">Webdesign</a>-->
            </div>
            
            </div> 
            <!-- contentbody end -->

    </div> <!-- wrapper end -->
<div class="clear">&nbsp;</div>
</body>
</html>