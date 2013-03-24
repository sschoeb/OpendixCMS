<h1>Administration - Links</h1>
<div id="contentbox">
<h2>Link hinzuf&uuml;gen</h2>
<form method="post" action="{$cms.link.add}">
<table>
	<tr>
		<td width="30px">Name: </td>
		<td><input type="text" name="newlink[name]" /></td>
	</tr>
	<tr>
		<td>Url:</td>
		<td><input type="text" name="newlink[url]" /></td>
	</tr>
	<tr>
		<td>Gruppe: </td>
		<td><select name="newlink[gId]">{include file="cms_select.tpl" selectOptions=$module.group}</select></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" name="add" value="Hinzuf&uuml;gen"/></td>
	</tr>
</table>
</form>
</div>
<br />

<form method="post" action="{$cms.link.save}" >
<table id="linkadmintabelle">
<tr>
	<th width="10%">Gruppe</th>
	<th width="45%">Name</th>
	<th width="45%">Url</th>
	<th></th>
</tr>
{foreach item=link from=$module.links}
{cssmodulo class=modulo varname=tmp}
<tr>
	<td class="{$tmp}"><select name="links[{$link.id}][gid]">{include file="cms_select.tpl" selectOptions=$link.group}</select></td>
	<td class="{$tmp}"><input type="text" name="links[{$link.id}][name]" value="{$link.name}" size="32" /></td>
	<td class="{$tmp}"><input type="text" name="links[{$link.id}][url]" value="{$link.url}" size="32"/></td>
	<td class="{$tmp}"><a href="{$link.delete}"><img src="{$cms.path.template.image}/delete.png" alt="L&ouml;schen" /></a></td>
</tr>
{/foreach}
</table>
<br />
<input type="submit" name="save" value="Speichern" /></form>