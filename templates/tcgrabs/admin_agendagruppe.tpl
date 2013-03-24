
<form action="{$opendix_addLink}" method="post">
<table align="center" width="90%">
	<tr>
		<td class="boxhead" colspan="3">
			Hinzuf&uuml;gen
		</td>
	</tr>
	<tr>
		<td>
			Name:
		</td>
		<td>
			<input type="text" name="name">
		</td>
		<td>
			<input type="submit" value="Hinzufügen">
		</td>
	</tr>
</table>

</form>

{if $show_ok == 1}
<form action="{$opendix_saveLink}" method="post">

<table align="center" width="90%">
	<tr>
		<td class="boxhead" colspan="3">
			Agenda-Gruppen
		</td>
	</tr>

{foreach item=show from=$show}
<tr>
<td>
<input type="text" name="name_{$show.i}" value="{$show.name}" size="40"><input type="hidden" name="mid_{$show.i}" value="{$show.id}" >
</td>
<td width="5%">
<a href="{$show.linkDelete}"><img src="{$opendix_medien}bilder/icons/del.gif" border="0" alt="Löschen"></a>

</td>
</tr>
{/foreach}
</table>

<br />
<div align="center"><input type="submit" value="Speichern" name="speichern"></div></form>


{/if}



