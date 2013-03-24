<?php /* Smarty version 2.6.18, created on 2011-04-03 09:00:10
         compiled from admin_sponsor_overview.tpl */ ?>
<h1>Administration - Sponsoren</h1>
<div id="contentbox">
<h2>Neuen Sponsor hinzuf&uuml;gen</h2>
<form method="POST" action="<?php echo $this->_tpl_vars['cms']['link']['add']; ?>
">Name: <input type="text"
	name="add[name]" /> <input type="submit" value="Hinzuf&uuml;gen" /> <br />
Gruppe: <select name="add[gId]">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_select.tpl", 'smarty_include_vars' => array('selectOptions' => $this->_tpl_vars['module']['groups'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</select><br />
</form>
</div>
<br />

<table id="basetabelle">
	<tr>
		<th style="width: 99%">Name</th>
		<th />
		<th />
		<th />
<th /><th />

	</tr>
	<?php $_from = $this->_tpl_vars['module']['sponsoren']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_59cb3a78b3ae908740d1cdb51e1ad20e):
        $this->_tpl_vars['sponsorgruppe'] = $_59cb3a78b3ae908740d1cdb51e1ad20e
?>
	<tr>
		<td class="modulo"><?php echo $this->_tpl_vars['sponsorgruppe']['name']; ?>
</td>
		<td class="modulo"><?php if (! $this->_tpl_vars['sponsorgruppe']['first']): ?><a href="<?php echo $this->_tpl_vars['sponsorgruppe']['links']['up']; ?>
"><img
			src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/up.gif"></a><?php endif; ?></td>
		<td class="modulo"><?php if (! $this->_tpl_vars['sponsorgruppe']['last']): ?><a href="<?php echo $this->_tpl_vars['sponsorgruppe']['links']['down']; ?>
"><img
			src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/down.gif"></a><?php endif; ?></td>
		<td class="modulo" colspan="3"></td>
	</tr>
	<?php $_from = $this->_tpl_vars['sponsorgruppe']['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_08391c959fc8bd0b672c596c4d6bcdcd):
        $this->_tpl_vars['sponsor'] = $_08391c959fc8bd0b672c596c4d6bcdcd
?>
	<tr>
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><a href="<?php echo $this->_tpl_vars['sponsor']['link']['item']; ?>
"><?php echo $this->_tpl_vars['sponsor']['name']; ?>
</a></td>
	<td></td><td></td>
		<td><?php if (! $this->_tpl_vars['sponsor']['first']): ?><a href="<?php echo $this->_tpl_vars['sponsor']['link']['up']; ?>
"><img
			src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/up.gif"></a><?php endif; ?></td>
		<td><?php if (! $this->_tpl_vars['sponsor']['last']): ?><a href="<?php echo $this->_tpl_vars['sponsor']['link']['down']; ?>
"><img
			src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/down.gif"></a><?php endif; ?></td>
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><a href="<?php echo $this->_tpl_vars['sponsor']['link']['delete']; ?>
"><img
			src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/delete.png" alt="L&ouml;schen" /></img></a></td>

	</tr>
	<?php endforeach; endif; unset($_from); ?> <?php endforeach; endif; unset($_from); ?>
</table>