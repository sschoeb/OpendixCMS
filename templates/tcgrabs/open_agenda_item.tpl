
<h1>{$module.termin.name}</h1>

<div id="anlassdaten">


<table class="kalender">
	<tr>
		<td width="130">Status</td>
		<td width="480">{agendastate state=$module.termin.state}</td>
	</tr>
	<tr>
		<td>Beginn</td>
		<td>{$module.termin.begin} Uhr</td>
	</tr>
	<tr>
		<td>Ende</td>
		<td>{$module.termin.end} Uhr</td>
	</tr>
	{if $module.termin.place != ''}
	<tr>
		<td>Treffpunkt</td>
		<td>{$module.termin.place}</td>
	</tr>
	{/if} 
	{if $module.termin.description != ''}
	<tr>
		<td valign="top">Bemerkung</td>
		<td>{$module.termin.description}</td>
	</tr>
	{/if}
</table>




{if $module.termin.contact != ''}
<hr />
<table class="ansprechpartner">
	<tr>
		<td colspan="2">
		<h2>Kontaktperson / Ansprechpartner</h2>
		</td>
	</tr>
	<tr>
		<td width="130">Name</td>
		<td width=480">{$module.termin.contact.details.firstname}
		{$module.termin.contact.details.name}</td>
	</tr>
	<tr>
		<td>Strasse</td>
		<td>{$module.termin.contact.details.street}</td>
	</tr>
	<tr>
		<td>Wohnort</td>
		<td>{$module.termin.contact.details.zip}
		{$module.termin.contact.details.residence}</td>
	</tr>
	{if $module.termin.contact.details.phoneprivate != ''}
	<tr>
		<td>Telefon (Privat)</td>
		<td>{$module.termin.contact.details.phoneprivate}</td>
	</tr>
	{/if}
	{if $module.termin.contact.details.phonebusiness != ''}
	<tr>
		<td>Telefon (Gesch&auml;ft)</td>
		<td>{$module.termin.contact.details.phonebusiness} </td>
	</tr>
	{/if}
	{if $module.termin.contact.details.phonemobile != ''}
	<tr>
		<td>Telefon (Mobile)</td>
		<td>{$module.termin.contact.details.phonemobile}</td>
	</tr>
	{/if}
	<tr>
		<td>E-Mail</td>
		<td>{mailto address=$module.termin.contact.details.email
		encode="javascript"}</td>
	</tr>
</table>
{/if}

{if count($module.termin.attachements) > 0}
<hr />


<table class="daten">
	<tr>
		<td colspan="2">
		<h2>Dateien zu dieser Veranstaltung</h2>
		</td>
	</tr>
	<tr>
		<td width="100" valign="top">Anh&auml;nge</td>
		<td width="510">
		{foreach item=attach from=$module.termin.attachements}
			<a href="{$attach.link.get}">{$attach.name}</a><br />
		{/foreach}
		</td>
	</tr>
	<!-- <tr>
		<td>Galerie</td>
		<td><a href="#">Galerie Clubversmamlung 2009</a></td>
	</tr>
	<tr>
		<td>Berichte</td>
		<td><a href="#">Bericht Clubversammlung 2009</a></td>
	</tr> -->
</table>
{/if}</div>

<br />
<br />