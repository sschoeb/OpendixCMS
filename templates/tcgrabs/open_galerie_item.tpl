<h1>{$module.galerie.name}</h1>
<p>{$module.galerie.description}</p>

{foreach item=img from=$module.images}
<div class="galeriebox">
	<a href="{$img.img}" rel="lightbox[]" style="background: #fff url('{$img.thumb}') 50% 50% no-repeat"></a>
</div>
{/foreach}



