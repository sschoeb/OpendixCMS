<?php /* Smarty version 2.6.18, created on 2011-04-03 10:44:44
         compiled from open_link.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cssmodulo', 'open_link.tpl', 8, false),)), $this); ?>
<h1>Links</h1>
<?php $_from = $this->_tpl_vars['module']['linkdata']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_db0f6f37ebeb6ea09489124345af2a45):
        $this->_tpl_vars['group'] = $_db0f6f37ebeb6ea09489124345af2a45
?>
	<h2><?php echo $this->_tpl_vars['group']['name']; ?>
</h2>
	<table id="basetabelle" summary="Links <?php echo $this->_tpl_vars['group']['name']; ?>
">

	<?php $_from = $this->_tpl_vars['group']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_2a304a1348456ccd2234cd71a81bd338):
        $this->_tpl_vars['link'] = $_2a304a1348456ccd2234cd71a81bd338
?>
	<tr>
		<?php echo smarty_function_cssmodulo(array('class' => 'modulo','varname' => 'tmp'), $this);?>

		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
" style="width: 300px"><a target="_blank" href="<?php echo $this->_tpl_vars['link']['url']; ?>
" title="<?php echo $this->_tpl_vars['link']['name']; ?>
"><?php echo $this->_tpl_vars['link']['url']; ?>
</a> </td>
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
" style="width: 400px"><?php echo $this->_tpl_vars['link']['name']; ?>
</td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
</table>
<?php endforeach; endif; unset($_from); ?>