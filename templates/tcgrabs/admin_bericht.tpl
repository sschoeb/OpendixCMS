{if $smarty.get.action == 'item' || $smarty.get.action == 'add' || $smarty.get.action == 'removegalerie' || $smarty.get.action == 'delanhang' || ($smarty.get.action == 'save' && $smarty.post.saveandclose == '')}
	{include file="admin_bericht_item.tpl"}
{else}
	{include file="admin_bericht_overview.tpl"}
{/if}