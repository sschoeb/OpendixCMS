
	{foreach item=css from=$cms.css}
		<style type="text/css">
		{include file="../../$css" imgPath=$imgPath} 	
		</style>


	{/foreach}

	{foreach item=jas from=$cms.javascript}
		<script src="{$jas}" type="text/javascript"></script>
	{/foreach}

	<script language="javascript" type="text/javascript">
	{foreach item=jas from=$cms.javascriptInside}
		{include file="../../$jas"}
	{/foreach}
	</script>