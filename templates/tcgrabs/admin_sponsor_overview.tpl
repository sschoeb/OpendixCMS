<h1>Administration - Sponsoren</h1>
<div id="contentbox">
<h2>Neuen Sponsor hinzuf&uuml;gen</h2>
<form method="POST" action="{$cms.link.add}">Name: <input type="text"
	name="add[name]" /> <input type="submit" value="Hinzuf&uuml;gen" /> <br />
Gruppe: <select name="add[gId]">
	{include file=cms_select.tpl selectOptions=$module.groups}
</select><br />
</form>
</div>
<br />

<table id="basetabelle">
	<tr>
		<th style="width: 99%">Name</th>
		<th />
		<th />
		<th />
<th /><th />

	</tr>
	{foreach item=sponsorgruppe from=$module.sponsoren}
	<tr>
		<td class="modulo">{$sponsorgruppe.name}</td>
		<td class="modulo">{if !$sponsorgruppe.first}<a href="{$sponsorgruppe.links.up}"><img
			src="{$cms.path.template.image}/up.gif"></a>{/if}</td>
		<td class="modulo">{if !$sponsorgruppe.last}<a href="{$sponsorgruppe.links.down}"><img
			src="{$cms.path.template.image}/down.gif"></a>{/if}</td>
		<td class="modulo" colspan="3"></td>
	</tr>
	{foreach item=sponsor from=$sponsorgruppe.items}
	<tr>
		<td class="{$tmp}"><a href="{$sponsor.link.item}">{$sponsor.name}</a></td>
	<td></td><td></td>
		<td>{if !$sponsor.first}<a href="{$sponsor.link.up}"><img
			src="{$cms.path.template.image}/up.gif"></a>{/if}</td>
		<td>{if !$sponsor.last}<a href="{$sponsor.link.down}"><img
			src="{$cms.path.template.image}/down.gif"></a>{/if}</td>
		<td class="{$tmp}"><a href="{$sponsor.link.delete}"><img
			src="{$cms.path.template.image}/delete.png" alt="L&ouml;schen" /></img></a></td>

	</tr>
	{/foreach} {/foreach}
</table>
