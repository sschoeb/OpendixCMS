<?php /* Smarty version 2.6.18, created on 2011-04-03 10:34:20
         compiled from admin_sponsor_item.tpl */ ?>
<h1>Administration - Sponsoren - Element</h1>
<form method="post" action="<?php echo $this->_tpl_vars['cms']['link']['save']; ?>
"
	enctype="multipart/form-data">
<div id="contentbox">
<h2>Allgemein</h2>
<table>
	<tr>
		<td width="100">Name:</td>
		<td><input type="text" name="name" value="<?php echo $this->_tpl_vars['module']['sponsor']['name']; ?>
" /></td>
	</tr>
	<tr>
		<td>Gruppe:</td>
		<td><select name="gId">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_select.tpl", 'smarty_include_vars' => array('selectOptions' => $this->_tpl_vars['module']['sponsor']['gruppe'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</select></td>
	</tr>
	<tr>
		<td>Link:</td>
		<td><input type="text" name="link" value="<?php echo $this->_tpl_vars['module']['sponsor']['url']; ?>
" /></td>
	</tr>
	<tr>
		<td style="vertical-align: top;">Beschreibung:</td>
		<td><textarea name="desc" cols="50" rows="5"><?php echo $this->_tpl_vars['module']['sponsor']['beschreibung']; ?>
</textarea></td>
	</tr>
		<tr><td></td>
		<td><input type="checkbox" name="frontpage" <?php echo $this->_tpl_vars['module']['sponsor']['frontpage']; ?>
 /> Auf
		Startseite anzeigen</td>
	</tr>
</table>
</div>

<br />
<div id="contentbox">
<h2>Bild</h2>
<?php if ($this->_tpl_vars['module']['sponsor']['bild'] != ''): ?>
<h3>Aktuelles Bild:</h3>
<img src="<?php echo $this->_tpl_vars['module']['sponsor']['bild']; ?>
" /> <br />
<br />
<?php endif; ?>
<h3>Neues Bild:</h3>
<input type="file" name="newImage" /></div>

<br />

<input type="submit" name="save" value="Speichern" /> <input
	type="submit" name="saveandclose" value="Speichern und Schliessen" /> <input
	type="submit" name="cancel" value="Abbrechen" /> <input type="reset"
	name="reset" value="Reset" /></form>