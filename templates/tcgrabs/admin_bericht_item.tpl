<form method="POST" action="{$cms.link.save}" name="formular">

<h1>Administration - Berichte - Element</h1>
<h2>Inhalt</h2>
{include file="cms_editor.tpl" editorvalue=$module.daten.html} <br />

<div id="contentbox">
<h2>Allgemein</h2>
<table>
	<tr>
		<td width="60px">Gruppe:</td>
		<td><select name="group">
			{include file="cms_select.tpl" selectOptions=$module.daten.gId}
		</select></td>
	</tr>
	<tr>
		<td>Titel:</td>
		<td><input type="text" name="title" value="{$module.daten.title}"
			size="70" /></td>
	</tr>
	<tr>
		<td>Aktiv:</td>
		<td><input type="checkbox" name="active" {$module.daten.active} /></td>
	</tr>
</table>
</div>
<br />
<div id="contentbox">
<h2>Anh&auml;nge</h2>
{if $module.daten.attachements != ''}
<h3>Bereits verlinkte Anh&auml;nge:</h3>
<ul>
	{foreach from=$module.daten.attachements item=attachement}
	<li><a href="{$attachement.links.get}">{$attachement.name}</a> &nbsp; <a
		href="{$attachement.links.delete}"><img
		src="{$cms.path.template.image}/delete.png" alt="L&ouml;schen" /></a></li>
	{/foreach}
</ul>
{/if}

<h3>Dateien hinzuf&uuml;gen</h3>
{include file="cms_fbbrowser.tpl" function="takeAgendaFile"}
<div id="selectedFiles" style="display: none;">Hinzugef&uuml;gte
Dateien: <br />
<ul id="selectedFilesTable">

</ul>
</div>
</div>
<br />
<!-- 
<div id="contentbox">
<h2>Verlinkte Gallerien</h2>
<h3>Verf&uuml;gbare Gallerien:</h3>
<select name="avaibleGallerie" id="avaibleGallerie">
	{include file="cms_select.tpl"
	selectOptions=$module.daten.avaibleGallerie}
</select><input type="button" onclick="javascript:linkgalery();"
	value="Verlinken" />

<div id="linkedgalleries" style="display: none;">
<h3>Verlinkte Gallerien:</h3>

<ul id="linkedList">

</ul>
</div>

{if $module.daten.gallery != ''} <br />
<h3>Bereits verlinkte Gallerien:</h3>
<ul>
	{foreach from=$module.daten.gallery item=gallery}
	<li>{$gallery.name} &nbsp;<a href="{$gallery.links.delete}">L&ouml;schen</a></li>
	{/foreach}
</ul>
{/if}</div>

<br /> -->

<input type="submit" value="Speichern" name="save" /> <input
	type="submit" value="Speichern und Schliessen" name="saveandclose" /> <input
	type="submit" value="Zur&uuml;cksetzen" name="reset" /></form>