<h1>Administration - Abos</h1>
<div id="contentbox">
<h2>Neues Abo hinzuf&uuml;gen</h2>
<form method="post" action="{$cms.link.add}">
	<label for="newData[name]" class="abolab">Name:</label>
	<input type="text" name="newData[name]" /><br />
	
	<label for="newData[pErwachsen]" class="abolab">Beschreibung:</label>
	<input type="text" name="newData[desc]" /><br />
	
	<label for="newData[pStudent]" class="abolab">Preis:</label>
	<input type="text" name="newData[preis]" /><br />
	
	<label for="addButton" class="abolab"> </label>
	<input type="submit" name="addButton" value="Abo hinzuf&uuml;gen" />
</form>
</div>
<br />
<h2>Bestehende Abos</h2>
<form method="post" action="{$cms.link.save}">
<table id="basetabelle" class="abotabelle">
	<tr>
		<th>Name</th>
		<th>Beschreibung</th>
		<th>Preis</th>
		<th colspan="2">Position</th>
		
	</tr>
	{foreach item=abo from=$module.data}
		<tr>
			<td><input  type="text" name="data[{$abo.id}][name]" value="{$abo.Name}" /></td>
			<td><input type="text" name="data[{$abo.id}][desc]" value="{$abo.Description}"/></td>
			<td><input type="text" name="data[{$abo.id}][preis]" value="{$abo.Preis}"/></td>
			<td><input type="text" name="data[{$abo.id}][folge]" value="{$abo.folge}"/></td>
			<td><a href="{$abo.links.delete}"><img src="{$cms.path.template.image}/delete.png"/></a></td>
		</tr>
	{/foreach}
</table>
<br/>
<input type="submit" value="Speichern" />
</form>