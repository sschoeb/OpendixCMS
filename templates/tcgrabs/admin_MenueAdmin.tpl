{if $smarty.get.action == 'item' || $smarty.get.action == 'add' }
	{include file="admin_MenueAdmin_item.tpl"}
{else}
	{include file="admin_MenueAdmin_overview.tpl"}
{/if}