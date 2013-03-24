<h1>Administration - G&auml;stebuch</h1>
<form action="{$module.gbook.links.delete}" method="post">

<table id="basetabelle">
	<tr>
		<th>&nbsp;</td>
		<th>Datum</td>
		<th>Ersteller</td>
		<th>Eintrag</td>
	</tr>
	{foreach item=item from=$module.gbook.daten}
	<tr>
		{cssmodulo class=modulo varname=tmp}
		<td width="1px" class="{$tmp}"><input type="checkbox" name="del[{$item.id}]"></td>
		<td class="{$tmp}">{$item.date}</td>
		<td class="{$tmp}">{$item.name}</td>
		<td class="{$tmp}"><a href="{$item.link}">{$item.eintrag}...</a></td>
	</tr>
	{/foreach}
</table>

<br />

{include file="cms_browse.tpl" info=$module.gbook.menue}

<input type="submit" value="markierte L&ouml;schen" name="loeschen" />

</form>