<?php /* Smarty version 2.6.18, created on 2012-02-15 19:47:17
         compiled from admin_persongroup.tpl */ ?>
<h1>Administration - Person - Gruppe</h1>
<form action="<?php echo $this->_tpl_vars['cms']['link']['add']; ?>
" method="post">
<div id="contentbox">
<h2>Neue Gruppe hinzuf&uuml;gen</h2>
<form method="POST" action="<?php echo $this->_tpl_vars['cms']['link']['add']; ?>
">
	Name: <input type="text"
	name="groupname" /><input type="submit" value="Hinzuf&uuml;gen" /></form>
</div>
<br />
<div id="contentbox">
<h2>Person zu Gruppe hinzuf&uuml;gen</h2>
<form method="POST" action="<?php echo $this->_tpl_vars['module']['newformAddLink']; ?>
">
<table>
<tr><td width="90px">Person:</td><td><select name="userId"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_select.tpl", 'smarty_include_vars' => array('selectOptions' => $this->_tpl_vars['module']['newformUser'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></select></td></tr>
<tr><td>Gruppe:</td><td><select name="groupId"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_select.tpl", 'smarty_include_vars' => array('selectOptions' => $this->_tpl_vars['module']['newformGroups'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></select></td></tr>
<tr><td>Funktion:</td><td><input type="text" name="funktion" /></td></tr>
<tr><td></td><td><input type="submit" value="Hinzuf&uuml;gen" /></td></tr></table>

</form>
</div>
<br />
<table id="basetabelle">
	<tr>
		<th colspan="4"  style="width:99%">Gruppen</th>

	
	</tr>
<?php $_from = $this->_tpl_vars['module']['modgroup_data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_8d777f385d3dfec8815d20f7496026dc):
        $this->_tpl_vars['data'] = $_8d777f385d3dfec8815d20f7496026dc
?>
<tr>
	<td colspan="3" class="modulo">
	<?php echo $this->_tpl_vars['data']['group']['name']; ?>
 
	</td>
	<td class="modulo">
		<!-- <a href="<?php echo $this->_tpl_vars['data']['group']['dellink']; ?>
"><img src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/delete.png" border="0" /></a> -->
	</td>
</tr>
<?php $_from = $this->_tpl_vars['data']['user']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_900d113bed0bacee782a9b9a1aafa6ae):
        $this->_tpl_vars['persondata'] = $_900d113bed0bacee782a9b9a1aafa6ae
?>
	<tr>
		<td>
			- <?php echo $this->_tpl_vars['persondata']['funktion']; ?>
 (<?php echo $this->_tpl_vars['persondata']['name']; ?>
 <?php echo $this->_tpl_vars['persondata']['firstname']; ?>
) 
		</td>
		<td>
			<?php if (! $this->_tpl_vars['persondata']['first']): ?><a href="<?php echo $this->_tpl_vars['persondata']['link']['up']; ?>
"><img src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/up.gif"></a><?php endif; ?>
		</td>
		<td>
			<?php if (! $this->_tpl_vars['persondata']['last']): ?><a href="<?php echo $this->_tpl_vars['persondata']['link']['down']; ?>
"><img src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/down.gif"></a><?php endif; ?>
		</td>
		<td>
			<a href="<?php echo $this->_tpl_vars['persondata']['link']['delete']; ?>
"><img src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/delete.png" border="0" /></a>
		</td>
	</tr>
<?php endforeach; endif; unset($_from);  endforeach; endif; unset($_from); ?>
</table>