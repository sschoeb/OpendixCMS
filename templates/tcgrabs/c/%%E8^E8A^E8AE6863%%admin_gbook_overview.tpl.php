<?php /* Smarty version 2.6.18, created on 2011-04-09 10:01:39
         compiled from admin_gbook_overview.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cssmodulo', 'admin_gbook_overview.tpl', 13, false),)), $this); ?>
<h1>Administration - G&auml;stebuch</h1>
<form action="<?php echo $this->_tpl_vars['module']['gbook']['links']['delete']; ?>
" method="post">

<table id="basetabelle">
	<tr>
		<th>&nbsp;</td>
		<th>Datum</td>
		<th>Ersteller</td>
		<th>Eintrag</td>
	</tr>
	<?php $_from = $this->_tpl_vars['module']['gbook']['daten']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_447b7147e84be512208dcc0995d67ebc):
        $this->_tpl_vars['item'] = $_447b7147e84be512208dcc0995d67ebc
?>
	<tr>
		<?php echo smarty_function_cssmodulo(array('class' => 'modulo','varname' => 'tmp'), $this);?>

		<td width="1px" class="<?php echo $this->_tpl_vars['tmp']; ?>
"><input type="checkbox" name="del[<?php echo $this->_tpl_vars['item']['id']; ?>
]"></td>
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><?php echo $this->_tpl_vars['item']['date']; ?>
</td>
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><?php echo $this->_tpl_vars['item']['name']; ?>
</td>
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><a href="<?php echo $this->_tpl_vars['item']['link']; ?>
"><?php echo $this->_tpl_vars['item']['eintrag']; ?>
...</a></td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
</table>

<br />

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_browse.tpl", 'smarty_include_vars' => array('info' => $this->_tpl_vars['module']['gbook']['menue'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<input type="submit" value="markierte L&ouml;schen" name="loeschen" />

</form>