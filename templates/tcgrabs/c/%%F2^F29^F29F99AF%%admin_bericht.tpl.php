<?php /* Smarty version 2.6.18, created on 2011-04-08 12:22:50
         compiled from admin_bericht.tpl */ ?>
<?php if ($_GET['action'] == 'item' || $_GET['action'] == 'add' || $_GET['action'] == 'removegalerie' || $_GET['action'] == 'delanhang' || ( $_GET['action'] == 'save' && $_POST['saveandclose'] == '' )): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "admin_bericht_item.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "admin_bericht_overview.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>