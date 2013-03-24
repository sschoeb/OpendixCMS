<?php /* Smarty version 2.6.18, created on 2011-04-08 10:43:30
         compiled from admin_abo.tpl */ ?>
<h1>Administration - Abos</h1>
<div id="contentbox">
<h2>Neues Abo hinzuf&uuml;gen</h2>
<form method="post" action="<?php echo $this->_tpl_vars['cms']['link']['add']; ?>
">
	<label for="newData[name]" class="abolab">Name:</label>
	<input type="text" name="newData[name]" /><br />
	
	<label for="newData[pErwachsen]" class="abolab">Beschreibung:</label>
	<input type="text" name="newData[desc]" /><br />
	
	<label for="newData[pStudent]" class="abolab">Preis:</label>
	<input type="text" name="newData[preis]" /><br />
	
	<label for="addButton" class="abolab"> </label>
	<input type="submit" name="addButton" value="Abo hinzuf&uuml;gen" />
</form>
</div>
<br />
<h2>Bestehende Abos</h2>
<form method="post" action="<?php echo $this->_tpl_vars['cms']['link']['save']; ?>
">
<table id="basetabelle" class="abotabelle">
	<tr>
		<th>Name</th>
		<th>Beschreibung</th>
		<th>Preis</th>
		<th colspan="2">Position</th>
		
	</tr>
	<?php $_from = $this->_tpl_vars['module']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_1739f7c0a0c6e4792dc494d2e7801208):
        $this->_tpl_vars['abo'] = $_1739f7c0a0c6e4792dc494d2e7801208
?>
		<tr>
			<td><input  type="text" name="data[<?php echo $this->_tpl_vars['abo']['id']; ?>
][name]" value="<?php echo $this->_tpl_vars['abo']['Name']; ?>
" /></td>
			<td><input type="text" name="data[<?php echo $this->_tpl_vars['abo']['id']; ?>
][desc]" value="<?php echo $this->_tpl_vars['abo']['Description']; ?>
"/></td>
			<td><input type="text" name="data[<?php echo $this->_tpl_vars['abo']['id']; ?>
][preis]" value="<?php echo $this->_tpl_vars['abo']['Preis']; ?>
"/></td>
			<td><input type="text" name="data[<?php echo $this->_tpl_vars['abo']['id']; ?>
][folge]" value="<?php echo $this->_tpl_vars['abo']['folge']; ?>
"/></td>
			<td><a href="<?php echo $this->_tpl_vars['abo']['links']['delete']; ?>
"><img src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/delete.png"/></a></td>
		</tr>
	<?php endforeach; endif; unset($_from); ?>
</table>
<br/>
<input type="submit" value="Speichern" />
</form>