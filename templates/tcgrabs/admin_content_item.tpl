<div id="contentbox">
<h2>Inhaltsseite bearbeiten</h2>
<form method="POST" action="{$cms.link.save}">
<table>
	<tr>
		<td width="50px">Name:</td>
		<td><input type="text" name="name" value="{$module.properties.name}" /></td>
	</tr>
	<tr>
		<td>Men&uuml;:</td>
		<td><select name="menue" id="menue">{$module.properties.menue}</select></td>
	</tr>

	<tr>
		<td>Aktiv:</td>
		<td><input name="active" type="checkbox" {$module.properties.active} /></td>
	</tr>
	<tr>
		<td>&Ouml;ffentlich:</td>
		<td><input name="common" type="checkbox" {$module.properties.common} /></td>
	</tr>
</table>

</div>
<br />
{include file="cms_editor.tpl" editorvalue=$module.properties.content}

<br />
<input type="submit" name="save" value="Speichern" />
<input type="submit" name="saveandclose"
	value="Speichern und Schliessen" />
<input type="submit" name="cancel" value="Abbrechen" />
<input type="reset" name="reset" value="Reset" />
</form>