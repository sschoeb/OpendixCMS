
{foreach item=group from=$selectOptions}
	
	{defun name="groupselrec" list=$group.items} 
		{foreach item=item from=$list}
			<option value="{$item.id}" {if $item.selected}selected="selected"{/if}>{str_repeat string="&nbsp;&nbsp;&nbsp;" count=$item.level}{$item.name}</option>
			{if $item.children != ''}
				{fun name="groupselrec" list=$item.children}
			{/if}
		{/foreach}
	{/defun}
{/foreach}