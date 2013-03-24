<form method="POST" action="{$cms.link.save}">
<h1>Men&uuml;punkt editieren</h1>
<h2>Eigenschaften</h2>
<table>
	<tr>
		<td>
			Modul:
		</td>
		<td>
			<select name="moduleid">
				{foreach item=moduleid from=$module.properties.moduleid}
					<option value="{$moduleid.value}" {if $moduleid.selected}selected="selected"{/if}>{$moduleid.name}</option>
				{/foreach}
			</select>
		</td>
		<td>
			Gruppen-ID
		</td>
		<td>
			<select name="groupid">
				{foreach item=groupid from=$module.properties.group}
					<option value="{$groupid.value}" {if $groupid.selected}selected="selected"{/if}>{$groupid.name}</option>
				{/foreach}
			</select>
		</td>		
	</tr>
	<tr>
		<td>
			Name
		</td>
		<td colspan="3">
			<input type="text" name="name" value="{$module.properties.name}" />
		</td>
	</tr>
	<tr>
		<td>
			Vater
		</td>
		<td>
			<select name="parent">
				{defun name="parentrecusrion" list=$module.properties.parent} 
					{foreach item=element from=$list}
					<option value="{$element.id}" {if $properties.parentselected == $element.id}selected="selected"{/if}>{$element.name|indent:$element.level:"->"}</option>
							{if $element.children != ''}
								{fun name="parentrecusrion" list=$element.children}
							{/if}
					{/foreach}
				{/defun}
			</select>
		</td>
		<td>
			Standard
		</td>
		<td>
			<input type="checkbox" name="stand"  {$module.properties.standard} />
		</td>		
	</tr>
	<tr>
		<td>
			Href
		</td>
		<td colspan="3">
			<input type="text" value="{$module.properties.href}" name="href" />
		</td>
	</tr>
	<tr>
		<td>
			Template
		</td>
		<td colspan="3">
			<select name="template">
				{foreach item=template from=$module.properties.template}
					<option value="{$template.value}" {if $template.selected}selected="selected"{/if}>{$template.name}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr>
		<td>
			Klasse
		</td>
		<td colspan="3">
			<input type="text" value="{$module.properties.class}" name="class" />
		</td>
	</tr>
	<tr>
		<td>
			Aktiv
		</td>
		<td>
			<input type="checkbox" name="active" {$module.properties.active} />
		</td>
		<td>
			Reihenfolge
		</td>
		<td>
			<input type="text" value="{$module.properties.order}" name="order" />
		</td>		
	</tr>
</table>

<h2>Berechtigungen</h2>
<input type="checkbox" name="common" {$module.law.common} /> Diese Seite ist &ouml;ffentlich
<h3>Gruppenberechtigungen</h3>
<table>
	<tr>
		<td>&nbsp;</td>
		<td>View</td>
		<td>Edit</td>
		<td>Add</td>
		<td>Delete</td>
	</tr>
	{foreach item=group from=$module.law.group}
		<tr>
			<td>{$group.name}</td>
			<td><input type="checkbox" name="g[{$group.id}][view]" {$group.law.view} /></td>
			<td><input type="checkbox" name="g[{$group.id}][edit]" {$group.law.edit} /></td>
			<td><input type="checkbox" name="g[{$group.id}][add]" {$group.law.add} /></td>
			<td><input type="checkbox" name="g[{$group.id}][delete]" {$group.law.delete} /></td>
		</tr>	
	{/foreach}
</table>
<h3>Benutzerberechtigungen</h3>
<table>
	<tr>
		<td>&nbsp;</td>
		<td>View</td>
		<td>Edit</td>
		<td>Add</td>
		<td>Delete</td>
	</tr>
	{foreach item=user from=$module.law.user}
		<tr>
			<td>{$user.name}</td>
			<td><input type="checkbox" name="u[{$user.id}][view]" {$user.law.view} /></td>
			<td><input type="checkbox" name="u[{$user.id}][edit]" {$user.law.edit} /></td>
			<td><input type="checkbox" name="u[{$user.id}][add]" {$user.law.add} /></td>
			<td><input type="checkbox" name="u[{$user.id}][delete]" {$user.law.delete} /></td>
		</tr>	
	{/foreach}
</table>

<input type="submit" value="Speichern"><input type="reset" value="Zur&uuml;cksetzen" />
</form>