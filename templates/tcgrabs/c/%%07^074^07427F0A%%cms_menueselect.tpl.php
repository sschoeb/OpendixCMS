<?php /* Smarty version 2.6.18, created on 2011-04-09 14:01:38
         compiled from cms_menueselect.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'str_repeat', 'cms_menueselect.tpl', 6, false),)), $this); ?>

<?php $_from = $this->_tpl_vars['selectOptions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_db0f6f37ebeb6ea09489124345af2a45):
        $this->_tpl_vars['group'] = $_db0f6f37ebeb6ea09489124345af2a45
?>
	
	<?php if (!function_exists('smarty_fun_groupselrec')) { function smarty_fun_groupselrec(&$smarty, $params) { $_fun_tpl_vars = $smarty->_tpl_vars; $smarty->assign($params);  ?> 
		<?php $_from = $smarty->_tpl_vars['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_447b7147e84be512208dcc0995d67ebc):
        $smarty->_tpl_vars['item'] = $_447b7147e84be512208dcc0995d67ebc
?>
			<option value="<?php echo $smarty->_tpl_vars['item']['id']; ?>
" <?php if ($smarty->_tpl_vars['item']['selected']): ?>selected="selected"<?php endif; ?>><?php echo smarty_function_str_repeat(array('string' => "&nbsp;&nbsp;&nbsp;",'count' => $smarty->_tpl_vars['item']['level']), $smarty); echo $smarty->_tpl_vars['item']['name']; ?>
</option>
			<?php if ($smarty->_tpl_vars['item']['children'] != ''): ?>
				<?php smarty_fun_groupselrec($smarty, array('list'=>$smarty->_tpl_vars['item']['children']));  ?>
			<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>
	<?php  $smarty->_tpl_vars = $_fun_tpl_vars; }} smarty_fun_groupselrec($this, array('list'=>$this->_tpl_vars['group']['items']));  ?>
<?php endforeach; endif; unset($_from); ?>