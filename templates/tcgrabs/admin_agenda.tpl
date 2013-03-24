{if $smarty.get.action == "item" || $smarty.post.save ||$smarty.get.action == "add" || $smarty.get.action == "delattachement" }
	{include file="admin_agenda_item.tpl"}	
{else if $smarty.get.action == "close" || $smarty.post.saveandclose || $smarty.post.cancel}
	{include file="admin_agenda_overview.tpl"}
{/if}


<!-- 
{if $smarty.get.action == 'editanmeldung'}
<form action="{$saveLink}" method="post">
<table align="center" width="90%">
	<tr>
		<td class="boxhead">
		Bemerkung
		</td>
	</tr>
	<tr>
		<td>
		{$editor}
		</td>
	</tr>
</table>

<br />

<table width="90%" align="center">
	<tr>
		<td class="boxhead" colspan="3">
			Formular
		</td>
	</tr>
	<tr class="tabelle_hervorgehoben">
		<td width="10%">
			<b>Anzeigen</b>
		</td>
		<td width="10%">
			<b>Pflicht</b>
		</td>
		<td width="80%">
			<b>Beschreibung</b>
		</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="anmeldeform[vorname]" {$agendaform.vorname} />
		</td>
		<td>
			<input type="checkbox" name="pflicht[vorname]" {$agendapflicht.vorname} />
		</td>
		<td>
			Vorname
		</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="anmeldeform[nachname]" {$agendaform.nachname} />
		</td>
		<td>
			<input type="checkbox" name="pflicht[nachname]" {$agendapflicht.nachname} />
		</td>
		<td>
			Nachname
		</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="anmeldeform[strasse]" {$agendaform.strasse} />
		</td>
		<td>
			<input type="checkbox" name="pflicht[strasse]" {$agendapflicht.strasse} />
		</td>
		<td>
			Strasse
		</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="anmeldeform[wohnort]" {$agendaform.wohnort} />
		</td>
		<td>
			<input type="checkbox" name="pflicht[wohnort]" {$agendapflicht.wohnort} />
		</td>
		<td>
			Wohnort
		</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="anmeldeform[email]" {$agendaform.email} />
		</td>
		<td>
			<input type="checkbox" name="pflicht[email]" {$agendapflicht.email} />
		</td>
		<td>
			E-Mail
		</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="anmeldeform[telefon]" {$agendaform.telefon} />
		</td>
		<td>
			<input type="checkbox" name="pflicht[telefon]" {$agendapflicht.telefon} />
		</td>
		<td>
			Telefon
		</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="anmeldeform[bemerkung]" {$agendaform.bemerkung} />
		</td>
		<td>
			<input type="checkbox" name="pflicht[bemerkung]" {$agendapflicht.bemerkung} />
		</td>
		<td>
			Bemerkung
		</td>
	</tr>
</table>

<br />

<table align="center" width="90%">
	<tr>
		<td class="boxhead" colspan="2">
			Best&auml;tigung
		</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="bes[benutzer]" {$agendaform.best_benutzer} />
		</td>
		<td>
			Anmeldebest&auml;tigung an den Benutzer schicken
		</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="bes[kontakt]" {$agendaform.bes_kontakt} />
		</td>
		<td>
			Anmeldebest&auml;tigung an Kontaktperson senden
		</td>
	</tr>
</table>


<br />

	<div align="center"><input type="submit" name="save" value="Speichern" /></div>
</form>
{/if}

{if $anmeldungen != ''}
<div align="center"><b><a href="{$zurueckLink}">Zur&uuml;ck</a></b></div>
{foreach item=anmeldung from=$anmeldungen}
		<table align="center" width="90%">
			<tr>
				<td class="boxhead" colspan="2">
					{$anmeldung.vorname} {$anmeldung.nachname}
				</td>
			</tr>
			<tr>
				<td width="25%">
					Strasse:
				</td>
				<td width="75%">
				{if $anmeldung.strasse == ''}
					<font color="Red">Keine Strasse angegeben!</font>
				{else}
					{$anmeldung.strasse}
				{/if}
				</td>
			</tr>
			<tr>
				<td>
					Wohnort:
				</td>
				<td>
				{if $anmeldung.wohnort == ''}
					<font color="Red">Keinen Wohnort angegeben!</font>
				{else}
					{$anmeldung.wohnort}
				{/if}
				</td>
			</tr>
			<tr>
				<td>
					Telefon:
				</td>
				<td>
				{if $anmeldung.telefon == ''}
					<font color="Red">Keine Telefonnummer angegeben!</font>
				{else}
					{$anmeldung.telefon}
				{/if}

				</td>
			</tr>
			<tr>
				<td>
					E-Mail
				</td>
				<td>

				{if $anmeldung.email == ''}
					<font color="Red">Keine E-Mail angegeben!</font>
				{else}
					<a href="mailto:{$anmeldung.email}">{$anmeldung.email}</a>
				{/if}
				</td>
			</tr>
			<tr>
				<td valign="top">
					Bemerkung:
				</td>
				<td>
				{if $anmeldung.bemerkung == ''}
					<font color="Red">Keine E-Mail angegeben!</font>
				{else}
					{$anmeldung.bemerkung}
				{/if}
				</td>
			</tr>
		</table>
		<br />
{/foreach}

<div align="center"><b><a href="{$zurueckLink}">Zur&uuml;ck</a></b></div>
{/if} -->