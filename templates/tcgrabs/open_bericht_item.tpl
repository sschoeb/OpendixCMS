<h1>{$module.item.general.title}</h1>
{$module.item.general.html}



{if $module.item.attachements != ''}
<hr />
<table class="daten">
	<tr>
		<td colspan="2">
		<h2>Dateien zu diesem Bericht</h2>
		</td>
	</tr>
	<tr>
		<td width="100" valign="top">Anh&auml;nge</td>
		<td width="510">{foreach item=attachement
		from=$module.item.attachements} <a href="{$attachement.link}" />{$attachement.name}</a>
		<br />
		{/foreach}</td>
	</tr>
</table>
{/if}
 
{if $module.item.gallery != ''} {* Alle angehängten Gallerien
anzeigen *} {foreach item=gallery from=$module.item.gallery}
<a href="{$gallery.link}" />{$gallery.name}</a>
<br />
{/foreach}
{/if}