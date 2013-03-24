<h1>Downloads verwalten</h1>
<div id="contentbox">
<h2>Downloads hinzuf&uuml;gen</h2>
<h3>Upload</h3>
<form action="{$cms.link.upload}" method="POST"
	enctype="multipart/form-data">
<div class="linkFile">
<table>
	<tr>
		<td>Datei ausw&auml;hlen:</td>
		<td><input type="file" name="file" size="40" /></td>
	</tr>
	<tr>
		<td>Gruppe:</td>
		<td><select name="uploadgroup">
			{include file="cms_select.tpl" selectOptions=$module.groups}
		</select></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" value="Upload" /></td>
	</tr>
</table>
</div>
</form>

<h3>Filebase</h3>
<div class="linkFile">
<form action="{$cms.link.save}" method="POST">{include
file="cms_fbbrowser.tpl" function="takeDownloadFile"}
<div id="selectedFiles" style="display: none;"><b>Neu verlinkte Dateien:</b>
<br />
<ul id="selectedFilesList">

</ul>
</div>

</div>
</div>
<br />
<div id="contentbox">
<h2>Verf&uuml;gbare Downloads</h2>

{foreach item=download from=$module.downloads} <b>{$download.fileId}
({filesize bytes=$download.size})</b>&nbsp;<a
	href="{$download.link.get}">Download</a> <br />
<table>
	{if $download.fileError}
	<tr>
		<td colspan="2">DIESE DATEI EXISTIERT NICHT!!</td>
	</tr>
	{/if}
	<tr>
		<td width="100px">Beschreibung:</td>
		<td><input type="text" value="{$download.description}"
			name="daten[{$download.id}][description]" size="60" /></td>
	</tr>
	<tr>
		<td>Alias:</td>
		<td><input type="text" value="{$download.fileAlias}"
			name="daten[{$download.id}][filealias]{$download.id}" size="60" /></td>
	</tr>
	<tr>
		<td>Reihenfolge:</td>
		<td><input type="text" value="{$download.order}"
			name="daten[{$download.id}][order]{$download.id}" size="60" /></td>
	</tr>

	<tr>
		<td>Gruppe:</td>
		<td><select name="daten[{$download.id}][group]">
			{include file="cms_select.tpl" selectOptions=$download.group}
		</select></td>
	</tr>
	<tr>
		<td></td>
		<td><a href="{$download.link.delete}"><img
			src="{$cms.path.template.image}/delete.png" />Loeschen</a></td>
	</tr>

</table>

<hr />
{/foreach}</div>
<br />
<!-- saveandclose damit von der Modulbase die Übersicht aufgerufen wird und nicht die Item-Methode¨-->
<input type="submit" value="Speichern" name="saveandclose" />
<input type="reset" value="Zur&uuml;cksetzen" name="reset" />

</form>