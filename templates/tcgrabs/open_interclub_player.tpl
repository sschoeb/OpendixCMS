
<iframe src="http://www.swisstennis.ch/custom/includes/public/getLizenzSpieler.cfm?action=lizenz_spieler&mitnr=1097&geschlecht=0&sortOrder=1&abfrage=1&Suchart=1" width="700" height="2000" frameBorder="0">asd</iframe>


<!-- {if $smarty.get.action == 'item'}
<h1>Lizenzierte Spieler - {$smarty.get.name}</h1>
<div class="interclub">
 -->
 asdasd

<!-- 
 <a href="{$module.link}" target="_blank">Resultate auf SwissTennis-Webseite</a>
{$module.player} 
</div>
{else}
<h1>Lizenzierte Spieler</h1>

<table id="basetabelle">
	<tr>
		<th>Lizenz-Nr.</th>
		<th>Name</th>
		<th>Klassierung</th>
		<th>Punkte</th>
	</tr>
	{foreach item=player from=$module.player}
	<tr>
		{cssmodulo class=modulo varname=tmp}
		<td class="{$tmp}">{$player.lizenznr}</td>
		<td class="{$tmp}"><a href="javascript:ShowPopup('{$player.links.item}', 650, 600);" >{$player.name}</a></td>
		<td class="{$tmp}">{$player.klass}</td>
		<td class="{$tmp}">{$player.klasswert}</td>
	</tr>
	{/foreach}
</table>
{/if}
 -->
