<?php /* Smarty version 2.6.18, created on 2011-04-03 08:49:30
         compiled from cms_content.tpl */ ?>
<?php if (count ( $this->_tpl_vars['warning'] ) > 0): ?>
<div class="warningbox">
<ul class="warning">
	<?php $_from = $this->_tpl_vars['warning']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_9a264b48f3792f3f768b75c4ed7c2dec):
        $this->_tpl_vars['warningItem'] = $_9a264b48f3792f3f768b75c4ed7c2dec
?>
		<li><?php echo $this->_tpl_vars['warningItem']; ?>
</li>
	<?php endforeach; endif; unset($_from); ?>
</ul>
</div>
<?php if (count ( $this->_tpl_vars['confirmation'] ) > 0): ?>
<br />
<?php endif; ?>
<?php endif; ?>

<?php if (count ( $this->_tpl_vars['confirmation'] ) > 0): ?>
<div class="confirmationbox">
<ul class="confirmation">
	<?php $_from = $this->_tpl_vars['confirmation']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_4a34faacbe2b9fd0a4a6c8d9b0a4281c):
        $this->_tpl_vars['confirmationItem'] = $_4a34faacbe2b9fd0a4a6c8d9b0a4281c
?>
		<li><?php echo $this->_tpl_vars['confirmationItem']; ?>
</li>
	<?php endforeach; endif; unset($_from); ?>
</ul>
</div>
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['cms']['content'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>