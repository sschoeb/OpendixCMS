<?php /* Smarty version 2.6.18, created on 2011-04-03 10:19:47
         compiled from admin_sponsorgroup.tpl */ ?>
<h1>Administration - Sponsoren - Gruppen</h1>
<div id="contentbox">
<h2>Neue Sponssoren-Gruppe hinzuf&uuml;gen</h2>
<form method="post" action="<?php echo $this->_tpl_vars['cms']['link']['add']; ?>
">
	<label for="newData[name]">Name:</label>
	<input type="text" name="newData[name]" />
	<input type="submit" name="addButton" value="Sponsoren-Gruppe hinzuf&uuml;gen" />
</form>
</div>
<br />
<h2>Bestehende Sponsoren-Gruppen</h2>
<form method="post" action="<?php echo $this->_tpl_vars['cms']['link']['save']; ?>
">
<table id="basetabelle" class="agendaadmintabelle">
	<tr>
		<th colspan="2">Name</th>
	</tr>
	<?php $_from = $this->_tpl_vars['module']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_1739f7c0a0c6e4792dc494d2e7801208):
        $this->_tpl_vars['abo'] = $_1739f7c0a0c6e4792dc494d2e7801208
?>
		<tr>
			<td><input  type="text" name="data[<?php echo $this->_tpl_vars['abo']['id']; ?>
][name]" value="<?php echo $this->_tpl_vars['abo']['name']; ?>
" size="80" /></td>
			<td><a href="<?php echo $this->_tpl_vars['abo']['links']['delete']; ?>
"><img src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/delete.png"/></a></td>
		</tr>
	<?php endforeach; endif; unset($_from); ?>
</table>
<br/>
<input type="submit" value="Speichern" />
</form>