
<h1>{$module.title}</h1>

{foreach item=person from=$module.persona}

<div class="person">
<div class="left">
<div class="foto"
	style="background: #fff url('{$person.bild}') 50% 50% no-repeat"></div>
</div>
<div class="right">
<h2>{$person.funktion}</h2>
<table>
	<tr>
		<td width="130">Name</td>
		<td>{$person.firstname} {$person.name}</td>
	</tr>
	<tr>
		<td>Adresse</td>
		<td>{$person.street}</td>
	</tr>
	<tr>
		<td>Ort</td>
		<td>{$person.zip} {$person.residence}</td>
	</tr>
	<tr>
		<td>E-Mail</td>
		<td><a href="mailto:">{mailto address=$person.email  encode="javascript"}</a></td>
	</tr>
	{if $person.phoneprivate != ''}
	<tr>
		<td>Telefon (Privat)</td>
		<td>{$person.phoneprivate}</td>
	</tr>
	{/if}
	{if $person.phonebusiness != ''}
	 <tr>
		<td>Telefon (Gesch&auml;ft)</td>
		<td>{$person.phonebusiness}</td>
	</tr>
	{/if}
	{if $person.phonemobile != ''}
	<tr>
		<td>Telefon (Mobil)</td>
		<td>{$person.phonemobile}</td>
	</tr> 
	{/if}
</table>
</div>
<div class="clear"></div>
</div>
{/foreach}
