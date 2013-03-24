<h1>Ein sportliches Willkommen beim Tennis Club Grabs</h1>



<!-- Aktuelle Nachrichten -->
<div class="container news">
<h2>Aktuelle Nachrichten</h2>
{foreach item=news from=$module.front.news} <tt>{$news.datum}</tt>
<p>{$news.name} {if $news.url != ""} [ <a href="{$news.url}">mehr</a> ]
{/if}</p>
{/foreach}</div>

<!-- Anlässe -->
<div class="container anlass">
<h2>N&auml;chste Anl&auml;sse</h2>
{foreach item=termin from=$module.front.termine} <tt>{$termin.begin}</tt>
<p>{$termin.name} [ <a href="{$termin.link}">mehr</a> ]</p>
{/foreach}</div>


<div class="clear"></div>


<!-- Newsletter -->
<div class="container newsletter">
<h2>Newsletter Abonnieren</h2>
<form name="newsletter" action="{$module.link.newsletter}" method="post">
	
	<select class="nlselect" name="action" size="1">
		<option value="1" selected="selected">In den Newsletter eintragen</option>
		<option value="2">Meine E-Mail aus dem Verteiler l&ouml;schen</option>
	</select> 
	<input class="nlinput" type="text" name="email" value="Ihre E-Mail Adresse" size="20" maxlength="50" /> <input
	class="nlsubmit" type="submit" name="submit" value="Absenden" />
	
</form>
</div>


<!-- Sponsor -->
<div class="container sponsor">
<h2>Official Sponsors</h2>
{if $module.front.sponsor.url != ''} <a
	href="{$module.front.sponsor.url}" target="_blank"> {/if} <img
	class="bild" src="{$module.front.sponsor.bild}" alt="Sponsor" /> {if
$module.front.sponsor.url != ''} </a> {/if}</div>


<div class="clear"></div>

