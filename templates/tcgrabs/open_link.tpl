<h1>Links</h1>
{foreach item=group from=$module.linkdata}
	<h2>{$group.name}</h2>
	<table id="basetabelle" summary="Links {$group.name}">

	{foreach item=link from=$group.data}
	<tr>
		{cssmodulo class=modulo varname=tmp}
		<td class="{$tmp}" style="width: 300px"><a target="_blank" href="{$link.url}" title="{$link.name}">{$link.url}</a> </td>
		<td class="{$tmp}" style="width: 400px">{$link.name}</td>
	</tr>
	{/foreach}
</table>
{/foreach}
