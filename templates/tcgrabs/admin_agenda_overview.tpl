<h1>Administration - Agenda</h1>


<div id="contentbox">
<h2>Neuer Termin hinzuf&uuml;gen</h2>
<form method="POST" action="{$cms.link.add}">Name: <input type="text"
	name="addname" /> <input type="submit" value="Hinzuf&uuml;gen" /></form>
</div>
<br />
<!-- <h1>Erfasste Termine</h1> -->
<table id="anlasstabelle" summary="Anlässe">
	<tr>
		<th style="width: 150px">Datum</th>
		<!-- <th style="width: 100px">Status</th> -->
		<th style="width: 500px">Veranstaltung</th>
		<!-- <th class="center" style="width: 20px">Aktiv</th> -->
		<th class="center" style="width: 20px"></th>
	</tr>


	{foreach item=anlass from=$module.dates.daten}

	<tr>
		{cssmodulo class=modulo varname=tmp}

		<td class="{$tmp}">{$anlass.begin}</td>
		<!-- <td class="{$tmp}">{if $anlass.state == 1} Durchgef&uuml;hrt {elseif
		$anlass.state == 2} Bevorstehend {else} Abgesagt {/if}</td> -->
		<td class="{$tmp}"><a href="{$anlass.link.alone}">{$anlass.name}</a></td>
		<!-- <td class="{$tmp}">Switch</td> -->
		<td class="{$tmp} center"><a href="{$anlass.link.del}"><img src="{$cms.path.template.image}/delete.png" alt="L&ouml;schen" /></a></td>
	</tr>

	{/foreach}
</table>

{include file="cms_browse.tpl" info=$module.dates.menue}