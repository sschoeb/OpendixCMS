<?php /* Smarty version 2.6.18, created on 2011-04-03 08:55:59
         compiled from cms_select.tpl */ ?>
<?php $_from = $this->_tpl_vars['selectOptions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_447b7147e84be512208dcc0995d67ebc):
        $this->_tpl_vars['item'] = $_447b7147e84be512208dcc0995d67ebc
?>
	<option value="<?php echo $this->_tpl_vars['item']['value']; ?>
" <?php if ($this->_tpl_vars['item']['selected']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['item']['name']; ?>
</option>
<?php endforeach; endif; unset($_from); ?>