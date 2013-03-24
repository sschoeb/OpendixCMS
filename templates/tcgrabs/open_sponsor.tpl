<h1>Sponsoren</h1>
{foreach item=spongroup from=$module.data}

<div id="contentbox">
<h2>{$spongroup.name}</h2>
<table>
	{foreach item=thesponsor from=$spongroup.items}
	<tr>
		<td class="sponsorrow{if $thesponsor.last}last{/if}" width="300px"><b>{$thesponsor.name}</b>
		{if $thesponsor.url != ""}(<a href="{$thesponsor.url}" target="_blank"
			title="{$thesponsor.name}">Webseite</a>){/if} <br />{$thesponsor.beschreibung}</td>
		<td class="sponsorrow{if $thesponsor.last}last{/if}">{if
		$thesponsor.bild != ""} {if $thesponsor.url != ""} <a
			href="{$thesponsor.url}" target="_blank" title="{$thesponsor.name}"><img
			src="{$thesponsor.bild}" alt="{$thesponsor.name}" /></a> {else} <img
			src="{$thesponsor.bild}" alt="{$thesponsor.name}"> {/if} {else} {/if}</td>
	</tr>
	{/foreach}
</table>
</div>
<br>

{/foreach}

