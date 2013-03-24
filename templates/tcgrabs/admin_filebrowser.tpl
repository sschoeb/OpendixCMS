<h1>Dateibrowser</h1>
<form method="POST" action="{$module.uploadLink}"
		enctype="multipart/form-data" style="display: inline;">
<div id="contentbox">
<h2>Dateiupload</h2>
<input type="file" name="fileUpload"><input type="submit" value="Upload">
</div>
</form>
<br />
<div id="contentbox">
<h2>Dateibrowser</h2>
<a href="{$module.rootLink}">Root /</a> {foreach item=navItem
from=$module.filebase_navLink} <a href="{$navItem.link}">{$navItem.name}</a>
{/foreach}
<h3>Dateisystem</h3>
<div id="fbbrowser">
<table style="width:575px;"">
	{if $module.filesBackLink != ''}
	<tr class="filerow">
		<td colspan="2"><a href="{$module.filesBackLink}"><img class="fileimg"
			src="{$cms.path.template.image}/dirup.png" border="0" />..</a></td>
	</tr>
	{/if} 

	{if count($module.directories) == 0 && count($module.files) == 0}
		<tr><td>Dieser Ordner ist leer</td></tr>
	{/if}

	{foreach item=dir from=$module.directories}
	<tr class="filerow">
		<td colspan="2"><a href="{$dir.link}"><img class="fileimg"
			src="{$cms.path.template.image}/dir.png">{$dir.name}</a></td>
	</tr>
	{/foreach} {foreach item=file from=$module.files}
	<tr class="filerow">
		<td><img src="{$cms.path.template.image}/file.gif" class="fileimg">{$file.name}</a></td>
		<td width="5%"><a href="{$file.link.del}"><img
			src="{$cms.path.template.image}/delete.png"></a></td>
	</tr>
	{/foreach}
</table>
</div>

<h3>Ordner erstellen</h3>
<form action="{$module.createFolderLink}" style="display: inline;"
			method="POST">Neuen Ordner erstellen: <input type="text"
			name="newFolder" /> <input type="submit" value="Erstellen"></form>
</div>

<table width="90%" align="center">
	
	<tr>
		<td></td>
	</tr>
	<tr>
		<td></td>
	</tr>
	
	<tr>
		<td>
		
		</td>
	</tr>
</table>
