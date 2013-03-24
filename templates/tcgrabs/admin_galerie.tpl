<h1>Administration - Galerie</h1>
{if $smarty.get.action == "item" || $smarty.post.save ||$smarty.get.action == "add" || $smarty.get.action == "removeImage" }
	{include file="admin_galerie_item.tpl"}	
{else if $smarty.get.action == "close" || $smarty.post.saveandclose || $smarty.post.cancel}
	{include file="admin_galerie_overview.tpl"}
{/if}
