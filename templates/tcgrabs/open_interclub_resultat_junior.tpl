<h1>Resultate Junioren</h1>
<iframe src="http://comp.swisstennis.ch//jic/servlet/ClubResult?ClubName=1097&Lang=D" width="670" height="1200" frameBorder="0"></iframe>

<!-- 

{if $smarty.get.action == 'item'}
<h1>Resultate - {$smarty.get.team}</h1>
	
<iframe src="{$module.link}" width="670" height="1200" frameBorder="0"></iframe>
	
{else}
<h1>Resultate</h1>
<table id="basetabelle">
	<tr>
		<th>Mannschaft</th>
	</tr>
	{foreach item=team from=$module.teams}
	<tr>
		{cssmodulo class=modulo varname=tmp}
		<td class="{$tmp}"><a href="{$team.link}">{$team.name}</a></td>
	</tr>
	{/foreach}
</table>
{/if}
 -->