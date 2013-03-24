{if $smarty.get.action == 'item'}
	{include file=open_galerie_item.tpl}
{else}
	{include file=open_galerie_overview.tpl}
{/if}