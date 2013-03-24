{foreach item=item from=$selectOptions}
	<option value="{$item.value}" {if $item.selected}selected="selected"{/if}>{$item.name}</option>
{/foreach}