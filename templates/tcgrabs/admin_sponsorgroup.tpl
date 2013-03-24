<h1>Administration - Sponsoren - Gruppen</h1>
<div id="contentbox">
<h2>Neue Sponssoren-Gruppe hinzuf&uuml;gen</h2>
<form method="post" action="{$cms.link.add}">
	<label for="newData[name]">Name:</label>
	<input type="text" name="newData[name]" />
	<input type="submit" name="addButton" value="Sponsoren-Gruppe hinzuf&uuml;gen" />
</form>
</div>
<br />
<h2>Bestehende Sponsoren-Gruppen</h2>
<form method="post" action="{$cms.link.save}">
<table id="basetabelle" class="agendaadmintabelle">
	<tr>
		<th colspan="2">Name</th>
	</tr>
	{foreach item=abo from=$module.data}
		<tr>
			<td><input  type="text" name="data[{$abo.id}][name]" value="{$abo.name}" size="80" /></td>
			<td><a href="{$abo.links.delete}"><img src="{$cms.path.template.image}/delete.png"/></a></td>
		</tr>
	{/foreach}
</table>
<br/>
<input type="submit" value="Speichern" />
</form>