<h1>Administration - Sponsoren - Element</h1>
<form method="post" action="{$cms.link.save}"
	enctype="multipart/form-data">
<div id="contentbox">
<h2>Allgemein</h2>
<table>
	<tr>
		<td width="100">Name:</td>
		<td><input type="text" name="name" value="{$module.sponsor.name}" /></td>
	</tr>
	<tr>
		<td>Gruppe:</td>
		<td><select name="gId">
			{include file=cms_select.tpl selectOptions=$module.sponsor.gruppe}
		</select></td>
	</tr>
	<tr>
		<td>Link:</td>
		<td><input type="text" name="link" value="{$module.sponsor.url}" /></td>
	</tr>
	<tr>
		<td style="vertical-align: top;">Beschreibung:</td>
		<td><textarea name="desc" cols="50" rows="5">{$module.sponsor.beschreibung}</textarea></td>
	</tr>
		<tr><td></td>
		<td><input type="checkbox" name="frontpage" {$module.sponsor.frontpage} /> Auf
		Startseite anzeigen</td>
	</tr>
</table>
</div>

<br />
<div id="contentbox">
<h2>Bild</h2>
{if $module.sponsor.bild!= ''}
<h3>Aktuelles Bild:</h3>
<img src="{$module.sponsor.bild}" /> <br />
<br />
{/if}
<h3>Neues Bild:</h3>
<input type="file" name="newImage" /></div>

<br />

<input type="submit" name="save" value="Speichern" /> <input
	type="submit" name="saveandclose" value="Speichern und Schliessen" /> <input
	type="submit" name="cancel" value="Abbrechen" /> <input type="reset"
	name="reset" value="Reset" /></form>