<h1>Administration - News</h1>
<div id="contentbox">
<h2>Neuer Newseintrag hinzuf&uuml;gen</h2>
<form method="POST" action="{$cms.link.add}">Name: <input type="text"
	name="name" /> <input type="submit" value="Hinzuf&uuml;gen" /></form>
</div>
<br />

<table id="basetabelle">
	<tr>
		<th style="width:99%">Name</th>
		<!-- <th>Aktiv</th> -->
		<th></th>
	
	</tr>
	{foreach item=news from=$module.overview}
	<tr>
		{cssmodulo class=modulo varname=tmp}
		<td class="{$tmp}"><a href="{$news.link.edit}">{$news.name}</a></td>
		<!-- <td class="{$tmp}"><a href="{$news.link.switchactive}">Switchactive</a></td> -->
		<td class="{$tmp}"><a href="{$news.link.delete}"><img src="{$cms.path.template.image}/delete.png" alt="L&ouml;schen" /></img></a></td>
	</tr>
	{/foreach}
</table>
