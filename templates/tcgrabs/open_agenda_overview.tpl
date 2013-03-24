<h1>Eine bestimmte Veranstaltung finden</h1>

{include file="open_agenda_filter.tpl"}

<br />
<br />
<h1>Termine und Veranstaltungen des Clubs</h1>
<table id="anlasstabelle" summary="Anlässe">
	<tr>
		<th style="width: 100px">Datum</th>
		<th style="width: 100px">Status</th>
		<th style="width: 400px">Veranstaltung</th>
		<th class="center" style="width: 20px">VCF</th>
	</tr>


	{foreach item=anlass from=$module.daten.daten}

	<tr>
		{cssmodulo class=modulo varname=tmp}

		<td class="{$tmp}">{$anlass.begin}</td>
		<td class="{$tmp}">{if $anlass.state == 1} Durchgef&uuml;hrt {elseif 
		$anlass.state == 2}  Bevorstehend{else} Abgesagt {/if}</td>
		<td class="{$tmp}"><a href="{$anlass.link.item}">{$anlass.name}</a></td>
		<td class="{$tmp} center"><a href="{$anlass.link.getvcs}"><img
			src="{$cms.path.template.image}/vcf.gif" width="16" height="11" style="border: 0px;" /></a></td>
	</tr>

	{/foreach}
</table>

{include file="cms_browse.tpl" info=$module.daten.menue}
