<h1>Administration - Personen - Element</h1>


<form method="post" action="{$cms.link.save}"
	enctype="multipart/form-data">
<div id="contentbox">
<h2>Kontakt</h2>
<table>
	<tr>
		<td style="width: 160px">Vorname:</td>
		<td><input type="text" name="user[firstname]"
			value="{$module.user.firstname}" size=27 /></td>
	</tr>
	<tr>
		<td>Nachname:</td>
		<td><input type="text" name="user[name]" value="{$module.user.name}"
			size=27 /></td>
	</tr>
	<tr>
		<td>E-Mail:</td>
		<td><input type="text" name="user[email]" value="{$module.user.email}"
			size=27 /></td>
	</tr>
	<tr>
		<td>Telefon (Privat):</td>
		<td><input type="text" name="user[phoneprivate]"
			value="{$module.user.phoneprivate}" size=27 /></td>
	</tr>
	<tr>
		<td>Telefon (Gesch&auml;ft):</td>
		<td><input type="text" name="user[phonebusiness]"
			value="{$module.user.phonebusiness}" size=27 /></td>
	</tr>
	<tr>
		<td>Telfon (Mobile):</td>
		<td><input type="text" name="user[phonemobile]"
			value="{$module.user.phonemobile}" size=27 /></td>
	</tr>
	<tr>
		<td>Adresse:</td>
		<td><input type="text" name="user[street]"
			value="{$module.user.street}" size=27 /></td>
	</tr>
	<tr>
		<td>Plz/Ort:</td>
		<td><input type="text" name="user[zip]" value="{$module.user.zip}"
			size=4 /> <input type="text" name="user[residence]"
			value="{$module.user.residence}" /></td>
	</tr>
</table>
</div>

<br />

<div class="person">
	<div class="left">
		<div class="foto" style="background: #fff ">{if $module.user.image != ''}<img src="{$module.user.image}"/>{/if}</div>
	</div>
	
	<div class="right">
		<h2>Anzeigebild</h2>
		<p>Neues Bild uploaden: </p>
		<input type="file" name="newImage" />
	</div>
	
	<div class="clear"></div>
</div>

<br />

<div id="contentbox">
<h2>Gruppenangeh&ouml;rigkeiten</h2>
{foreach item=group from=$module.usergroupsPers} {$group.name}<br />
{/foreach} {foreach item=group from=$module.usergroupsAdmin} <font
	color="orange">{$group.name} (System)</font><br />
{/foreach}</div>

<br />

<input type="submit" name="save" value="Speichern" /> <input
	type="submit" name="saveandclose" value="Speichern und Schliessen" /> <input
	type="submit" name="cancel" value="Abbrechen" /> <input type="reset"
	name="reset" value="Reset" /></form>