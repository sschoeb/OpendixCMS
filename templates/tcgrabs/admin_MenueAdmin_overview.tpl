

<h1>Men&uuml;punkte editieren</h1>
<h2>Men&uuml;punkt hinzuf&uuml;gen</h2>
<form method="POST" action="{$cms.link.add}">
<input type="text" name="name" /> <input type="submit" value="Hinzuf&uuml;gen" />
</form>
<h2>Vorhandene Men&uuml;struktur</h2>
{foreach item=group from=$module.menueData}
<h3>{$group.name}</h3>
	{defun name="menuadminrecursion" list=$group.items} 
		{foreach item=element from=$list}
		<a href="{$element.link}">{$element.name|indent:$element.level:"->"}</a><br>
				{if $element.children != ''}
					{fun name="menuadminrecursion" list=$element.children}
				{/if}
		{/foreach}
	{/defun}
{/foreach}