<h1>Bildergalerien rund um den Tennis Club Grabs</h1>

{if count($module.galeries.daten)==0}
Es sind leider noch keine Galerien vorhanden.
{/if}

{foreach item=galerie from=$module.galeries.daten}
	<div class="galeriebox">
		<h2>{$galerie.name}</h2>
		<a href="{$galerie.link}" style="background: #fff url('{$galerie.thumb}') 50% 50% no-repeat"></a>
		<p>{$galerie.imgCount} Bilder (Datum: {$galerie.date})</p>
		<img class="lupe" src="{$cms.path.template.image}/lupe.png" />
	</div>
{/foreach}

<div class="clear"></div>

{include file="cms_browse.tpl" info=$module.galeries.menue}

