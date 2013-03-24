<?php /* Smarty version 2.6.18, created on 2011-04-09 14:12:53
         compiled from admin_newsletter.tpl */ ?>
<h1>Administration - Newsletter</h1>
<form method="post" action="<?php echo $this->_tpl_vars['cms']['link']['save']; ?>
">
<textarea name="data" rows="30" cols="80">
<?php $_from = $this->_tpl_vars['module']['emails']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_0c83f57c786a0b4a39efab23731c7ebc):
        $this->_tpl_vars['email'] = $_0c83f57c786a0b4a39efab23731c7ebc
?>
<?php echo $this->_tpl_vars['email']['email']; ?>
;
<?php endforeach; endif; unset($_from); ?>
</textarea>
<input type="submit" name="save" value="Speichern" />
</form>