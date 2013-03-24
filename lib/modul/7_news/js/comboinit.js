{literal}

function update_auswahl(){
	var kategorieAuswahl = document.forms.news.type;
	var unterkategorieAuswahl = document.forms.news.typeId;
  	unterkategorieAuswahl.options.length = 0; 
{/literal}{foreach item=type from=$type}
	{if $type.type == 1}
		{literal}
			if (kategorieAuswahl.options[kategorieAuswahl.selectedIndex].{/literal}value == "{$type.tname}"{literal})
			{{/literal}{/if}{if $type.type != 1}{literal}
			     unterkategorieAuswahl.options[{/literal}{$type.j}{literal}] = new Option("{/literal}{$type.name}{literal}","{/literal}{$type.id}{literal}");{/literal}{/if}{if $type.type == 2}{literal}
			unterkategorieAuswahl.options[{/literal}{$type.j}{literal}].selected = true;
		
			{/literal}{/if}{if $type.close == 1}{literal}}
{/literal}{/if}{/foreach}{literal}}{/literal}