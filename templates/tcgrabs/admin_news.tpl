
{if $smarty.get.action == "item" || $smarty.post.save ||$smarty.get.action == "add" }
	{include file="admin_news_item.tpl"}	
{else if $smarty.get.action == "close" || $smarty.post.saveandclose || $smarty.post.cancel}
	{include file="admin_news_overview.tpl"}
{/if}



