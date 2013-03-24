<?php /* Smarty version 2.6.18, created on 2013-03-12 21:07:43
         compiled from admin_content_item.tpl */ ?>
<div id="contentbox">
<h2>Inhaltsseite bearbeiten</h2>
<form method="POST" action="<?php echo $this->_tpl_vars['cms']['link']['save']; ?>
">
<table>
	<tr>
		<td width="50px">Name:</td>
		<td><input type="text" name="name" value="<?php echo $this->_tpl_vars['module']['properties']['name']; ?>
" /></td>
	</tr>
	<tr>
		<td>Men&uuml;:</td>
		<td><select name="menue" id="menue"><?php echo $this->_tpl_vars['module']['properties']['menue']; ?>
</select></td>
	</tr>

	<tr>
		<td>Aktiv:</td>
		<td><input name="active" type="checkbox" <?php echo $this->_tpl_vars['module']['properties']['active']; ?>
 /></td>
	</tr>
	<tr>
		<td>&Ouml;ffentlich:</td>
		<td><input name="common" type="checkbox" <?php echo $this->_tpl_vars['module']['properties']['common']; ?>
 /></td>
	</tr>
</table>

</div>
<br />
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_editor.tpl", 'smarty_include_vars' => array('editorvalue' => $this->_tpl_vars['module']['properties']['content'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<br />
<input type="submit" name="save" value="Speichern" />
<input type="submit" name="saveandclose"
	value="Speichern und Schliessen" />
<input type="submit" name="cancel" value="Abbrechen" />
<input type="reset" name="reset" value="Reset" />
</form>