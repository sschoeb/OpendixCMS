<h1>Administration - Personen</h1>
<div id="contentbox">
<h2>Neue Person hinzuf&uuml;gen</h2>
<form method="POST" action="{$cms.link.add}">Vorname: <input type="text"
	name="firstname" /> 
	Nachname: <input type="text"
	name="name" /><input type="submit" value="Hinzuf&uuml;gen" /></form>
</div>
<br />

<table id="basetabelle">
	<tr>
		<th style="width:99%">Name</th>
		<!-- <th>Aktiv</th> -->
		<th></th>
	
	</tr>
	{foreach item=person from=$module.personen}
	<tr>
		{cssmodulo class=modulo varname=tmp}
		<td class="{$tmp}"><a href="{$person.link.edit}">{$person.name}</a></td>
		<!-- <td class="{$tmp}"><a href="{$news.link.switchactive}">Switchactive</a></td> -->
		<td class="{$tmp}">{if !$person.nodelete}<a href="{$person.link.delete}"><img src="{$cms.path.template.image}/delete.png" alt="L&ouml;schen" /></img></a>{/if}</td>
	</tr>
	{/foreach}
</table>