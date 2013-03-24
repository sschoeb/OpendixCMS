<?php /* Smarty version 2.6.18, created on 2011-04-05 22:14:35
         compiled from open_bericht_item.tpl */ ?>
<h1><?php echo $this->_tpl_vars['module']['item']['general']['title']; ?>
</h1>
<?php echo $this->_tpl_vars['module']['item']['general']['html']; ?>




<?php if ($this->_tpl_vars['module']['item']['attachements'] != ''): ?>
<hr />
<table class="daten">
	<tr>
		<td colspan="2">
		<h2>Dateien zu diesem Bericht</h2>
		</td>
	</tr>
	<tr>
		<td width="100" valign="top">Anh&auml;nge</td>
		<td width="510"><?php $_from = $this->_tpl_vars['module']['item']['attachements']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_ddbc3c54dcad2801bbee817df94e83c6):
        $this->_tpl_vars['attachement'] = $_ddbc3c54dcad2801bbee817df94e83c6
?> <a href="<?php echo $this->_tpl_vars['attachement']['link']; ?>
" /><?php echo $this->_tpl_vars['attachement']['name']; ?>
</a>
		<br />
		<?php endforeach; endif; unset($_from); ?></td>
	</tr>
</table>
<?php endif; ?>
 
<?php if ($this->_tpl_vars['module']['item']['gallery'] != ''): ?>  <?php $_from = $this->_tpl_vars['module']['item']['gallery']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_2767cc3ede7592a47bd6657e3799565c):
        $this->_tpl_vars['gallery'] = $_2767cc3ede7592a47bd6657e3799565c
?>
<a href="<?php echo $this->_tpl_vars['gallery']['link']; ?>
" /><?php echo $this->_tpl_vars['gallery']['name']; ?>
</a>
<br />
<?php endforeach; endif; unset($_from);  endif; ?>