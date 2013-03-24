<h1>Administration - Person - Gruppe</h1>
<form action="{$cms.link.add}" method="post">
<div id="contentbox">
<h2>Neue Gruppe hinzuf&uuml;gen</h2>
<form method="POST" action="{$cms.link.add}">
	Name: <input type="text"
	name="groupname" /><input type="submit" value="Hinzuf&uuml;gen" /></form>
</div>
<br />
<div id="contentbox">
<h2>Person zu Gruppe hinzuf&uuml;gen</h2>
<form method="POST" action="{$module.newformAddLink}">
<table>
<tr><td width="90px">Person:</td><td><select name="userId">{include file=cms_select.tpl selectOptions=$module.newformUser}</select></td></tr>
<tr><td>Gruppe:</td><td><select name="groupId">{include file=cms_select.tpl selectOptions=$module.newformGroups}</select></td></tr>
<tr><td>Funktion:</td><td><input type="text" name="funktion" /></td></tr>
<tr><td></td><td><input type="submit" value="Hinzuf&uuml;gen" /></td></tr></table>

</form>
</div>
<br />
<table id="basetabelle">
	<tr>
		<th colspan="4"  style="width:99%">Gruppen</th>

	
	</tr>
{foreach item=data from=$module.modgroup_data}
<tr>
	<td colspan="3" class="modulo">
	{$data.group.name} 
	</td>
	<td class="modulo">
		<!-- <a href="{$data.group.dellink}"><img src="{$cms.path.template.image}/delete.png" border="0" /></a> -->
	</td>
</tr>
{foreach item=persondata from=$data.user}
	<tr>
		<td>
			- {$persondata.funktion} ({$persondata.name} {$persondata.firstname}) 
		</td>
		<td>
			{if !$persondata.first}<a href="{$persondata.link.up}"><img src="{$cms.path.template.image}/up.gif"></a>{/if}
		</td>
		<td>
			{if !$persondata.last}<a href="{$persondata.link.down}"><img src="{$cms.path.template.image}/down.gif"></a>{/if}
		</td>
		<td>
			<a href="{$persondata.link.delete}"><img src="{$cms.path.template.image}/delete.png" border="0" /></a>
		</td>
	</tr>
{/foreach}
{/foreach}
</table>