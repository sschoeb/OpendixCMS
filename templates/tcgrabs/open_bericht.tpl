{if $smarty.get.action == 'item' || $smarty.get.action=='getattachement'}
	{include file=open_bericht_item.tpl}
{elseif $smarty.get.action == 'showgallery'}
	{include file=open_gallerie.tpl}
{else}
	{include file=open_bericht_overview.tpl}
{/if}