<?php /* Smarty version 2.6.18, created on 2011-04-11 22:04:58
         compiled from cms_import.tpl */ ?>

	<?php $_from = $this->_tpl_vars['cms']['css']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_c7a628cba22e28eb17b5f5c6ae2a266a):
        $this->_tpl_vars['css'] = $_c7a628cba22e28eb17b5f5c6ae2a266a
?>
		<style type="text/css">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../../".($this->_tpl_vars['css']), 'smarty_include_vars' => array('imgPath' => $this->_tpl_vars['imgPath'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 	
		</style>


	<?php endforeach; endif; unset($_from); ?>

	<?php $_from = $this->_tpl_vars['cms']['javascript']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_c70903749ed556d98a4966fdfb9ccd04):
        $this->_tpl_vars['jas'] = $_c70903749ed556d98a4966fdfb9ccd04
?>
		<script src="<?php echo $this->_tpl_vars['jas']; ?>
" type="text/javascript"></script>
	<?php endforeach; endif; unset($_from); ?>

	<script language="javascript" type="text/javascript">
	<?php $_from = $this->_tpl_vars['cms']['javascriptInside']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_c70903749ed556d98a4966fdfb9ccd04):
        $this->_tpl_vars['jas'] = $_c70903749ed556d98a4966fdfb9ccd04
?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../../".($this->_tpl_vars['jas']), 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endforeach; endif; unset($_from); ?>
	</script>