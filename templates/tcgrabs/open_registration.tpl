<h1>Anmeldung</h1>
<p>Du kannst dich direkt &uuml;ber unsere Webseite beim Tennisclub Grabs
anmelden. Bitte w&auml;hle das passende Abo aus der folgenden Tabelle
aus und sende deine Anmeldung &uuml;ber das Formular zu uns. Wir werden
dir nach der Anmeldung weitere Infos zukommen lassen.</p>
<br />
<table id="basetabelle">
	<tr>
		<th>Abo</th>
		<th>Beschreibung</th>
		<th>Preis</th>
	</tr>
	{foreach item=abo from=$module.abos} {cssmodulo class=modulo
	varname=tmp}
	<tr>
		<td class="{$tmp}">{$abo.Name}</td>
		<td class="{$tmp}">{$abo.Description}</td>
		<td class="{$tmp}">{$abo.Preis}.-</td>
	</tr>
	{/foreach}
</table>
<br />
<div id="contentbox">
<h2>Anmeldung</h2>
<form method="POST" action="{$module.link}"><label class="reglab"
	for="regdata[name]">Name:</label> <input type="text"
	name="regdata[name]" class="reginput" /><br />

<label class="reglab" for="regdata[firstname]">Vorname:</label> <input
	type="text" name="regdata[firstname]" class="reginput" /><br />

<label class="reglab" for="regdata[adress]">Adresse:</label> <input
	type="text" name="regdata[adress]" class="reginput" /><br />

<label class="reglab" for="regdata[zip]">Plz/Ort:</label> <input
	type="text" name="regdata[zip]" class="reginputzip" /><input
	type="text" name="regdata[place]" class="reginputplace" /><br />

<label class="reglab" for="regdata[email]">E-Mail:</label> <input
	type="text" name="regdata[email]" class="reginput" /><br />


<label class="reglab" for="regdata[phoneprivate]">Telefon (Privat):</label> <input
	type="text" name="regdata[phoneprivate]" class="reginput" /><br />


<label class="reglab" for="regdata[phonegesch]">Telefon (Gesch.):</label> <input
	type="text" name="regdata[phonegesch]" class="reginput" /><br />

<label class="reglab" for="regdata[phonemobile]">Mobile:</label> <input
	type="text" name="regdata[phonemobile]" class="reginput" /><br />

<label class="reglab" for="regdata[phone]">Nationalit&auml;t:</label> <input
	type="text" name="regdata[national]" class="reginput" /><br />

<label class="reglab" for="regdata[phone]">Geburtstag:</label> 
		{html_select_date prefix="" field_array="regdata" time=$module.termin.begin display_months=false display_years=false}
	{html_select_date prefix="" field_array="regdata" time=$module.termin.begin start_year="-90"  display_days=false}
<br />

<label class="reglab" for="regdata[type]">Abo:</label> <select
	name="regdata[abo]" class="reginputsel">
	{include file=cms_select.tpl selectOptions=$module.abosSelect}
</select><br />

<label class="reglab" for="regdata[spam]">Spamschutz: 7 + 1 =</label><input
	type="text" name="regdata[spam]" class="reginput" /><br />

<label class="reglab">&nbsp;</label> <input type="submit" name="send"
	value="Anmeldung abschicken" /></form>
</div>