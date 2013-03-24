<h1>Administration - Agenda - Element</h1>
<form action="{$cms.link.save}" method="post" enctype="multipart/form-data">

<div id="contentbox">
<h2>Allgemein</h2>
<table>
	<tr>
		<td width="150px">Aktiv</td>
		<td><input type="checkbox" name="termin[active]" {$module.termin.active} />
		</td>
	</tr>
	<tr>
		<td>Status</td>
		<td><select name="termin[state]">
			<option value="2"{$module.termin.state.before}>Bevorstehend</option>
			<option value="1"{$module.termin.state.finished}>Durchgef&uuml;hrt</option>
			<option value="3"{$module.termin.state.canceled}>Abgesagt</option>
		</select></td>
	</tr>
	<tr>
		<td>Gruppe</td>
		<td><select name="termin[agendaId]">
			{include file="cms_select.tpl" selectOptions=$module.termin.agenda}
		</select></td>
	</tr>
	<tr>
		<td>Titel</td>
		<td><input type="text" name="termin[name]"
			value="{$module.termin.name}" /></td>
	</tr>
	<tr>
		<td>Treffpunkt</td>
		<td><input type="text" name="termin[place]" value="{$module.termin.place}" /></td>
	</tr>
</table>
</div>
<br />

<div id="contentbox">
<h2>Bermerkung</h2>
<table>
	<tr>
		<td>
			<textarea name="opendixckeditor" cols="70" rows=6>{$module.termin.description}</textarea>
			
</td>
	</tr>
</table>
</div>
<br />

<div id="contentbox">
<h2>Kontaktperson</h2>
<table>
	<tr>
		<td width="150px">Name/Vorname</td>
		<td><select name="termin[contactId]">
			<option value="NULL"></option>
			{include file="cms_select.tpl" selectOptions=$module.termin.kontakt}
		</select></td>
	</tr>
</table>
</div>

<br>

<div id="contentbox">
<h2>Zeit</h2>
<table>
	<tr>
		<td width="150px">Beginn</td>
		<td>
		{html_select_date prefix="" field_array="termin[time][begin]" time=$module.termin.begin display_months=false display_years=false}
	{html_select_date prefix="" field_array="termin[time][begin]" time=$module.termin.begin end_year="+5"  display_days=false}
	{html_select_time prefix="" field_array="termin[time][begin]" use_24_hours=true display_seconds=false time=$module.termin.begin}
</td>
	</tr>
	<tr>
		<td>Ende</td>
		<td>
		{html_select_date prefix="" field_array="termin[time][end]" time=$module.termin.end display_months=false display_years=false}
	{html_select_date prefix="" field_array="termin[time][end]" time=$module.termin.end end_year="+5"  display_days=false}
	{html_select_time prefix="" field_array="termin[time][end]" use_24_hours=true display_seconds=false time=$module.termin.end}
</tr>
	<tr>
		<td></td>
		<td><input type="checkbox" name="openEnd"{$module.termin.openEnd}>
		Offenes Ende</td>
	</tr>
</table>
</div>
<!-- 
{include file="cms_timer.tpl" timerdata=$module.termin.timer} <br />
 
<!-- 
<div id="contentbox">
<h2>Anmeldung</h2>
<table>
	<tr>
		<td>Anmeldeschluss</td>
		<td>{html_select_date prefix=anmeldung_schluss
		time=$termin.anmeldung_schluss display_months=false
		display_years=false} {html_select_date prefix=anmeldung_schluss
		time=$termin.anmeldung_schluss end_year="+5" display_days=false}

		{html_select_time prefix=anmeldung_schluss use_24_hours=true
		display_seconds=false time=$termin.anmeldung_schluss}</td>
	</tr>
	<tr>
		<td style="border: none;"><input type="checkbox"
			name="termin[anmeldung_moeglich]" {$termin.anmeldung_moeglich} /></td>
		<td style="border: none;">Anmeldung m&ouml;glich</td>
	</tr>
	<tr>
		<td style="border: none;"><img
			src="{$opendix_medien}/bilder/icons/anmeldung.gif" /></td>
		<td align="left" valign="middle" style="border: none;"><a
			href="{$termin.link.editanmeldung}">Anmeldeformular bearbeiten</a></td>
	</tr>
	<tr>
		<td style="border: none;"><input type="checkbox"
			name="termin[anmeldung_einsehen]" {$termin.anmeldung_einsehen} /></td>
		<td style="border: none;">Angemeldete einsehen</td>
	</tr>
	<tr>
		<td style="border: none;"><img
			src="{$opendix_medien}/bilder/icons/anmeldung.gif" /></td>
		<td align="left" valign="middle" style="border: none;">{if
		$termin.anzanmeldungen > 0} <a href="{$termin.link.anmeldungen}">Anmeldungen
		({$termin.anzanmeldungen})</a> {else} Anmeldungen
		({$termin.anzanmeldungen}) {/if}</td>
	</tr>
</table>
</div>
<br /> -->
<div id="contentbox">
<h2>Bericht</h2>

<table>
	<tr>
		<td width="150px">Berich Men&uuml;punkt:</td>
		<td><select id="berichtmenueid" name="termin[berichtMenueId]"
	onchange="javascript:ChangeBerichtMenueId();">
	<option>Keine Verlinkung vorhanden</option>
			{include file="cms_select.tpl"
	selectOptions=$module.termin.berichtmenueid}

		</select></td>
	</tr>
	<tr id="berichtIdLabel">
		<td width="150px">Angeh&auml;ngter Bericht:</td>
		<td><select id="berichtid" name="termin[berichtId]">
			{$modul.termin.berichtId}

		</select></td>
	</tr>
</table>
</div>
<br />

<div id="contentbox">
<h2>Anh&auml;nge</h2>
Dateien aus der Filedatenbank hinzuf&uuml;gen:
{include file="cms_fbbrowser.tpl" function="takeAgendaFile"}
<div id="selectedFiles" style="display:none;">
 <br />
	<h3>Hinzugef&uuml;gte Dateien:</h3>
	<table id="selectedFilesTable">

	</table>
</div>

{if $module.termin.attachement != ''}
<h3>Verlinkte Anh&auml;nge</h3>
<ul>
	{foreach item=attachement from=$module.termin.attachement}
	<li>
			<a href="{$attachement.link.get}">{$attachement.name}</a>
			<a href="{$attachement.link.delete}">L&ouml;schen</a>
	</li>
	{/foreach}
</ul>

{/if}
</div>
<!-- 
<table>
	<tr>
		<td colspan="2">Dateien aus der Filedatenbank
		hinzuf&uuml;gend&nbsp;&nbsp;&nbsp; <input type="button"
			id="fileBrowserButton" value="Filebrowser anzeigen"
			onclick="javascript:switchFileBrowser();"><br />
		<div id="fileTest"></div>
		</td>
	</tr>
	<tr id="newFileRow" style="display: none;">
		<td valign="top">Ausgew&auml;hlte Dateien:</td>
		<td id="newFileCol"></td>
	</tr>
	{if $modul.termin.anhang != ''}
	<tr>
		<td colspan="2">Verlinkte Anhänge:</td>
	</tr>
	{foreach item=anhang from=$modul.termin.anhang}
	<tr>
		<td colspan="2" style="border: none;"><a href="{$anhang.link.del}"><img
			src="{$opendix_medien}/bilder/icons/x.png" border="0" /></a> <a
			href="{$anhang.link.get}">{$modul.anhang.name}</a></td>
	</tr>
	{/foreach} {/if}
</table>
 -->

<br />
<input type="submit" name="save" value="Speichern" /> <input
	type="submit" name="saveandclose" value="Speichern und Schliessen" /> <input
	type="submit" name="cancel" value="Abbrechen" /> <input type="reset"
	name="reset" value="Reset" /></form>