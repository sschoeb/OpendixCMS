<?php /* Smarty version 2.6.18, created on 2011-04-08 12:22:55
         compiled from admin_bericht_overview.tpl */ ?>
<h1>Administration - Berichte</h1>

<div id="contentbox">
<h2>Neuer Bericht hinzuf&uuml;gen</h2>
<form method="POST" action="<?php echo $this->_tpl_vars['cms']['link']['add']; ?>
">Name: <input type="text"
	name="addname" /> <br />Gruppe:
<select name="addgroup">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_select.tpl", 'smarty_include_vars' => array('selectOptions' => $this->_tpl_vars['module']['groups'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</select> <input type="submit" value="Hinzuf&uuml;gen" /></form>
</div>
<br />

<?php $_from = $this->_tpl_vars['module']['berichte']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_db0f6f37ebeb6ea09489124345af2a45):
        $this->_tpl_vars['group'] = $_db0f6f37ebeb6ea09489124345af2a45
?>
<h2><?php echo $this->_tpl_vars['group']['name']; ?>
</h2>
<table id="basetabelle">
	<tr>
		<th colspan="2">Name</th>
	</tr>
	<?php $_from = $this->_tpl_vars['group']['berichte']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_2d9a4b2333ea678cc93f413ce61afaa0):
        $this->_tpl_vars['bericht'] = $_2d9a4b2333ea678cc93f413ce61afaa0
?>
	<tr>
		<td><a href="<?php echo $this->_tpl_vars['bericht']['links']['item']; ?>
"><?php echo $this->_tpl_vars['bericht']['title']; ?>
</a></td>
		<td><a href="<?php echo $this->_tpl_vars['bericht']['links']['delete']; ?>
"><img
			src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/delete.png" alt="L&ouml;schen" /></a></td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>

</table>
<?php endforeach; endif; unset($_from); ?>