<?php /* Smarty version 2.6.18, created on 2011-04-03 10:43:13
         compiled from open_download.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cssmodulo', 'open_download.tpl', 10, false),array('function', 'filesize', 'open_download.tpl', 12, false),)), $this); ?>
<?php $_from = $this->_tpl_vars['module']['downloads']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_db0f6f37ebeb6ea09489124345af2a45):
        $this->_tpl_vars['group'] = $_db0f6f37ebeb6ea09489124345af2a45
?>
<h1><?php echo $this->_tpl_vars['group']['name']; ?>
</h1>
<table id="downloadtabelle" summary="Downloads <?php echo $this->_tpl_vars['group']['name']; ?>
">
    <tr>
    	<th style="width: 500px">Name</th>
    	<th style="width: 100px; text-align: right;">Gr&ouml;sse</th>
    </tr>
	<?php $_from = $this->_tpl_vars['group']['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_447b7147e84be512208dcc0995d67ebc):
        $this->_tpl_vars['item'] = $_447b7147e84be512208dcc0995d67ebc
?>
	<tr>
		<?php echo smarty_function_cssmodulo(array('class' => 'modulo','varname' => 'tmp'), $this);?>

		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><a href="<?php echo $this->_tpl_vars['item']['link']; ?>
"><?php echo $this->_tpl_vars['item']['name']; ?>
</a> </td>
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
" style="text-align: right;"><?php echo smarty_function_filesize(array('bytes' => $this->_tpl_vars['item']['size']), $this);?>
</td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
</table>
<?php endforeach; endif; unset($_from); ?>