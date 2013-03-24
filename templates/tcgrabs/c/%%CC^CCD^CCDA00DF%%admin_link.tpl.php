<?php /* Smarty version 2.6.18, created on 2011-04-15 18:08:51
         compiled from admin_link.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cssmodulo', 'admin_link.tpl', 36, false),)), $this); ?>
<h1>Administration - Links</h1>
<div id="contentbox">
<h2>Link hinzuf&uuml;gen</h2>
<form method="post" action="<?php echo $this->_tpl_vars['cms']['link']['add']; ?>
">
<table>
	<tr>
		<td width="30px">Name: </td>
		<td><input type="text" name="newlink[name]" /></td>
	</tr>
	<tr>
		<td>Url:</td>
		<td><input type="text" name="newlink[url]" /></td>
	</tr>
	<tr>
		<td>Gruppe: </td>
		<td><select name="newlink[gId]"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_select.tpl", 'smarty_include_vars' => array('selectOptions' => $this->_tpl_vars['module']['group'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></select></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" name="add" value="Hinzuf&uuml;gen"/></td>
	</tr>
</table>
</form>
</div>
<br />

<form method="post" action="<?php echo $this->_tpl_vars['cms']['link']['save']; ?>
" >
<table id="linkadmintabelle">
<tr>
	<th width="10%">Gruppe</th>
	<th width="45%">Name</th>
	<th width="45%">Url</th>
	<th></th>
</tr>
<?php $_from = $this->_tpl_vars['module']['links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_2a304a1348456ccd2234cd71a81bd338):
        $this->_tpl_vars['link'] = $_2a304a1348456ccd2234cd71a81bd338
?>
<?php echo smarty_function_cssmodulo(array('class' => 'modulo','varname' => 'tmp'), $this);?>

<tr>
	<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><select name="links[<?php echo $this->_tpl_vars['link']['id']; ?>
][gid]"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_select.tpl", 'smarty_include_vars' => array('selectOptions' => $this->_tpl_vars['link']['group'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></select></td>
	<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><input type="text" name="links[<?php echo $this->_tpl_vars['link']['id']; ?>
][name]" value="<?php echo $this->_tpl_vars['link']['name']; ?>
" size="32" /></td>
	<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><input type="text" name="links[<?php echo $this->_tpl_vars['link']['id']; ?>
][url]" value="<?php echo $this->_tpl_vars['link']['url']; ?>
" size="32"/></td>
	<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><a href="<?php echo $this->_tpl_vars['link']['delete']; ?>
"><img src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/delete.png" alt="L&ouml;schen" /></a></td>
</tr>
<?php endforeach; endif; unset($_from); ?>
</table>
<br />
<input type="submit" name="save" value="Speichern" /></form>