<form method="POST" action="{$cms.link.add}">
<div id="contentbox">
<h2>Inhaltsseite hinzuf&uuml;gen</h2>

<input type="text" name="name" /> <input type="submit"
	value="Hinzuf&uuml;gen" name="AddBtn" /></div>
</form>
<br />


<table id="basetabelle">
	<tr>
		<th style="width: 99%">Name</th>
		<!-- <th>Aktiv</th> -->
		<th></th>

	</tr>
	{foreach item=content from=$module.contents.daten}
	<tr>
		{cssmodulo class=modulo varname=tmp}
		<td class="{$tmp}"><a href="{$content.link.item}">{$content.name}</a></td>
		<!-- <td class="{$tmp}"><a href="{$content.link.switchactive}">Switchactive</a></td> -->
		<td class="{$tmp}"><a href="{$content.link.delete}"><img
			src="{$cms.path.template.image}/delete.png" alt="L&ouml;schen" /></img></a></td>
	</tr>
	{/foreach}
</table>

