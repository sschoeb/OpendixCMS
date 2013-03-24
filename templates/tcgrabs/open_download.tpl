{foreach item=group from=$module.downloads}
<h1>{$group.name}</h1>
<table id="downloadtabelle" summary="Downloads {$group.name}">
    <tr>
    	<th style="width: 500px">Name</th>
    	<th style="width: 100px; text-align: right;">Gr&ouml;sse</th>
    </tr>
	{foreach item=item from=$group.items}
	<tr>
		{cssmodulo class=modulo varname=tmp}
		<td class="{$tmp}"><a href="{$item.link}">{$item.name}</a> </td>
		<td class="{$tmp}" style="text-align: right;">{filesize bytes=$item.size}</td>
	</tr>
	{/foreach}
</table>
{/foreach}