<h1>Administration - Berichte</h1>

<div id="contentbox">
<h2>Neuer Bericht hinzuf&uuml;gen</h2>
<form method="POST" action="{$cms.link.add}">Name: <input type="text"
	name="addname" /> <br />Gruppe:
<select name="addgroup">
	{include file=cms_select.tpl selectOptions=$module.groups}
</select> <input type="submit" value="Hinzuf&uuml;gen" /></form>
</div>
<br />

{foreach from=$module.berichte item=group}
<h2>{$group.name}</h2>
<table id="basetabelle">
	<tr>
		<th colspan="2">Name</th>
	</tr>
	{foreach from=$group.berichte item=bericht}
	<tr>
		<td><a href="{$bericht.links.item}">{$bericht.title}</a></td>
		<td><a href="{$bericht.links.delete}"><img
			src="{$cms.path.template.image}/delete.png" alt="L&ouml;schen" /></a></td>
	</tr>
	{/foreach}

</table>
{/foreach}
