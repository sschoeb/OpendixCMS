<div id="contentbox">
<h2>Neue Galerie hinzuf&uuml;gen</h2>
<form method="POST" action="{$cms.link.add}">Name: <input type="text"
	name="name" /> <input type="submit" value="Hinzuf&uuml;gen" /></form>
</div>
<br />

<table id="basetabelle">
	<tr>
		<th style="width:99%">Name</th>
		<th></th>
	
	</tr>
	{foreach item=galerie from=$module.galerien.daten}
	<tr>
		{cssmodulo class=modulo varname=tmp}
		<td class="{$tmp}"><a href="{$galerie.link.edit}">{$galerie.name}</a></td>
		<td class="{$tmp}"><a href="{$galerie.link.delete}"><img src="{$cms.path.template.image}/delete.png" alt="L&ouml;schen" /></img></a></td>
	</tr>
	{/foreach}
</table>

{include file="cms_browse.tpl" info=$module.galerien.menue}