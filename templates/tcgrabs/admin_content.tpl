<h1>Administration - Inhalt</h1>
{if $smarty.get.action == "item" || $smarty.post.save ||$smarty.get.action == "add" }
	{include file="admin_content_item.tpl"}	
{else if $smarty.get.action == "close" || $smarty.post.saveandclose || $smarty.post.cancel}
	{include file="admin_content_overview.tpl"}
{/if}

