<h1>{$module.title}</h1>
<table id="berichttabelle" summary="Berichte">
    <tr>
    	<th style="width: 100px">Erstellungsdatum</th>
        <th style="width: 500px">Name</th>
    </tr>
	{foreach item=bericht from=$module.berichte}
	
	{cssmodulo class=modulo varname=tmp}
	
	<tr>
		<td class="{$tmp}">{$bericht.datum}</td>
		<td class="{$tmp}"><a href="{$bericht.links.item}">{$bericht.title}</a> </td>
	</tr>
	{/foreach}
</table>