{if $smarty.get.action == 'item'}
	{include file=open_agenda_item.tpl}
{else}
	{include file=open_agenda_overview.tpl}
{/if}