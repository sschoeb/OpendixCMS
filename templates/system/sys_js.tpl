
	{foreach item=jas from=$javascript}
		<script src="js/{$jas}" type="text/javascript"></script>
	{/foreach}

<script language="javascript" type="text/javascript">
	{foreach item=jas from=$javascriptInside}
		{include file="js/$jas"}
	{/foreach}

	
	
function run()
	{literal}{{/literal}
		{foreach item=run from=$runatstart}
			{$run}
		{/foreach}
	{literal}}{/literal}
</script>