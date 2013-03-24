<?php /* Smarty version 2.6.18, created on 2011-04-06 18:17:57
         compiled from admin_galerie.tpl */ ?>
<h1>Administration - Galerie</h1>
<?php if ($_GET['action'] == 'item' || $_POST['save'] || $_GET['action'] == 'add' || $_GET['action'] == 'removeImage'): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "admin_galerie_item.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>	
<?php else: ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "admin_galerie_overview.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>