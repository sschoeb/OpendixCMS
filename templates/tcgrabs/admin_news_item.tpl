<form method="POST" action="{$cms.link.save}"
	enctype="multipart/form-data">
<h1>Administration - News - Element</h1>
<div id="contentbox">
<h2>Allgemein</h2>
<table>
	<tr>
		<td>Name:</td>
		<td><input type="text" name="news[name]" size="60" value="{$module.item.name}" /></td>
	</tr>
	<!-- <tr>
		<td>Text:</td>
		<td><textarea name="news[text]">{$module.item.text}</textarea></td>
	</tr> -->
	<tr>
		<td>Zeit:</td>
		<td>
		{html_select_date prefix="" field_array="news[time]"
		time=$module.item.time display_months=false display_years=false}
		{html_select_date prefix="" field_array="news[time]"
		time=$module.item.time end_year="+5" display_days=false}
		{html_select_time prefix="" field_array="news[time]"
		time=$module.item.time use_24_hours=true display_seconds=false }</td>
	</tr>
	<tr>
		<td></td>
		<td><input type="checkbox" name="news[active]" {$module.item.active} />
		Aktiv</td>
	</tr>
</table>

<!-- Startseite: <input type="checkbox" name="news[frontpage]" {$module.item.frontpage} /><br />
Panel: <input type="checkbox" name="news[panel]" {$module.item.panel} /><br /> -->

<!-- newsfeed: <input type="checkbox" name="news[newsfeed]" {$module.item.newsfeed} /> -->
</div>

<br />
<div id="contentbox">
<h2>Verlinkung</h2>
<h3>Links hinzuf&uuml;gen</h3>
<input id="linkname" type="hidden" name="news[link][name]" value="newLink" />
<input type="radio" name="news[link][type]" value="FILE" onchange="javascript:radioBoxChange();" {if $module.item.linktype== "FILE"}checked{/if} />Filebase<br />
<div class="linkFile" id="linkFile">
 {include file="cms_fbbrowser.tpl" function="takeNewsFile"}<br />

Ausgew&auml;hlte Datei: <br /><input type="text" id="filebaseFile" name="news[link][filebase]" value="{$module.item.filebase}" size="50" readonly /><br />
</div> 

<input type="radio" name="news[link][type]" value="WEBSITE" onchange="javascript:radioBoxChange();" {if $module.item.linktype== "WEBSITE"}checked{/if}/>Webseite<br />
<div class="linkFile" id="linkWebsite">
Verlinkte Webseite:<br />
<input type="text" id="linkWebsiteInput" name="news[website]" size="50" value="{$module.item.website}" /><br />
</div> <input type="radio" name="news[link][type]" value="INTERNAL"
	onchange="javascript:radioBoxChange();" {if $module.item.linktype== "INTERNAL"}checked{/if}/>Interne
Verlinkung<br />
<div class="linkFile" id="linkInternal"> <select id="connmenue" name="menueitem"
	onchange="javascript:connMenueChange();">
	{include file="cms_menueselect.tpl"
	selectOptions=$module.item.menueselect}
</select> <br />
<span id="sconnconn"> <select id="connconn"
	name="news[link][internal][connectyiontype]"
	onchange="javascript:connConnChange();" />
</select> <br />
</span> <span id="sconnelement"> <select id="connelement"
	name="news[link][internal][element]" />
</select> <br />
</span></div> <input type="button" onclick="javascript:AddNewLink();"
	value="Link hinzuf&uuml;gen"\> </span> <span id="linklistcontainer">
<h3>Vorhandene Links</h3>
<ul id="avaibleLinks">
	{foreach item=link from=$module.item.links}
	<li id="linkId{$link.id}"><a href="{$link.link.link}"
		{if $link.linktype !="FILEBASE" } target="_blank"{/if}>{$link.name}</a>
	| <a href="javascript:DeleteLinkClick({$link.id});">L&ouml;schen</a></li>
	{/foreach}
</ul>
</span></div>
<br />


<br />
<input type="submit" name="save" value="Speichern" /> <input
	type="submit" name="saveandclose" value="Speichern und Schliessen" /> <input
	type="submit" name="cancel" value="Abbrechen" /> <input type="reset"
	name="reset" value="Reset" /></form>