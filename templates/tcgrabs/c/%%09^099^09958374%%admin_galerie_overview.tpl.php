<?php /* Smarty version 2.6.18, created on 2012-08-25 18:42:57
         compiled from admin_galerie_overview.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cssmodulo', 'admin_galerie_overview.tpl', 16, false),)), $this); ?>
<div id="contentbox">
<h2>Neue Galerie hinzuf&uuml;gen</h2>
<form method="POST" action="<?php echo $this->_tpl_vars['cms']['link']['add']; ?>
">Name: <input type="text"
	name="name" /> <input type="submit" value="Hinzuf&uuml;gen" /></form>
</div>
<br />

<table id="basetabelle">
	<tr>
		<th style="width:99%">Name</th>
		<th></th>
	
	</tr>
	<?php $_from = $this->_tpl_vars['module']['galerien']['daten']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_c564752a1e971d9e2d09d9cba750a13c):
        $this->_tpl_vars['galerie'] = $_c564752a1e971d9e2d09d9cba750a13c
?>
	<tr>
		<?php echo smarty_function_cssmodulo(array('class' => 'modulo','varname' => 'tmp'), $this);?>

		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><a href="<?php echo $this->_tpl_vars['galerie']['link']['edit']; ?>
"><?php echo $this->_tpl_vars['galerie']['name']; ?>
</a></td>
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><a href="<?php echo $this->_tpl_vars['galerie']['link']['delete']; ?>
"><img src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/delete.png" alt="L&ouml;schen" /></img></a></td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
</table>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_browse.tpl", 'smarty_include_vars' => array('info' => $this->_tpl_vars['module']['galerien']['menue'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>