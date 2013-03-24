{if count($warning) > 0}
<div class="warningbox">
<ul class="warning">
	{foreach item=warningItem from=$warning}
		<li>{$warningItem}</li>
	{/foreach}
</ul>
</div>
{if count($confirmation) > 0}
<br />
{/if}
{/if}

{if count($confirmation) > 0}
<div class="confirmationbox">
<ul class="confirmation">
	{foreach item=confirmationItem from=$confirmation}
		<li>{$confirmationItem}</li>
	{/foreach}
</ul>
</div>
{/if}

{include file=$cms.content}