<?php /* Smarty version 2.6.18, created on 2011-04-08 12:35:19
         compiled from admin_bericht_item.tpl */ ?>
<form method="POST" action="<?php echo $this->_tpl_vars['cms']['link']['save']; ?>
" name="formular">

<h1>Administration - Berichte - Element</h1>
<h2>Inhalt</h2>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_editor.tpl", 'smarty_include_vars' => array('editorvalue' => $this->_tpl_vars['module']['daten']['html'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> <br />

<div id="contentbox">
<h2>Allgemein</h2>
<table>
	<tr>
		<td width="60px">Gruppe:</td>
		<td><select name="group">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_select.tpl", 'smarty_include_vars' => array('selectOptions' => $this->_tpl_vars['module']['daten']['gId'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</select></td>
	</tr>
	<tr>
		<td>Titel:</td>
		<td><input type="text" name="title" value="<?php echo $this->_tpl_vars['module']['daten']['title']; ?>
"
			size="70" /></td>
	</tr>
	<tr>
		<td>Aktiv:</td>
		<td><input type="checkbox" name="active" <?php echo $this->_tpl_vars['module']['daten']['active']; ?>
 /></td>
	</tr>
</table>
</div>
<br />
<div id="contentbox">
<h2>Anh&auml;nge</h2>
<?php if ($this->_tpl_vars['module']['daten']['attachements'] != ''): ?>
<h3>Bereits verlinkte Anh&auml;nge:</h3>
<ul>
	<?php $_from = $this->_tpl_vars['module']['daten']['attachements']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_ddbc3c54dcad2801bbee817df94e83c6):
        $this->_tpl_vars['attachement'] = $_ddbc3c54dcad2801bbee817df94e83c6
?>
	<li><a href="<?php echo $this->_tpl_vars['attachement']['links']['get']; ?>
"><?php echo $this->_tpl_vars['attachement']['name']; ?>
</a> &nbsp; <a
		href="<?php echo $this->_tpl_vars['attachement']['links']['delete']; ?>
"><img
		src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/delete.png" alt="L&ouml;schen" /></a></li>
	<?php endforeach; endif; unset($_from); ?>
</ul>
<?php endif; ?>

<h3>Dateien hinzuf&uuml;gen</h3>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_fbbrowser.tpl", 'smarty_include_vars' => array('function' => 'takeAgendaFile')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="selectedFiles" style="display: none;">Hinzugef&uuml;gte
Dateien: <br />
<ul id="selectedFilesTable">

</ul>
</div>
</div>
<br />
<!-- 
<div id="contentbox">
<h2>Verlinkte Gallerien</h2>
<h3>Verf&uuml;gbare Gallerien:</h3>
<select name="avaibleGallerie" id="avaibleGallerie">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_select.tpl", 'smarty_include_vars' => array('selectOptions' => $this->_tpl_vars['module']['daten']['avaibleGallerie'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</select><input type="button" onclick="javascript:linkgalery();"
	value="Verlinken" />

<div id="linkedgalleries" style="display: none;">
<h3>Verlinkte Gallerien:</h3>

<ul id="linkedList">

</ul>
</div>

<?php if ($this->_tpl_vars['module']['daten']['gallery'] != ''): ?> <br />
<h3>Bereits verlinkte Gallerien:</h3>
<ul>
	<?php $_from = $this->_tpl_vars['module']['daten']['gallery']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_2767cc3ede7592a47bd6657e3799565c):
        $this->_tpl_vars['gallery'] = $_2767cc3ede7592a47bd6657e3799565c
?>
	<li><?php echo $this->_tpl_vars['gallery']['name']; ?>
 &nbsp;<a href="<?php echo $this->_tpl_vars['gallery']['links']['delete']; ?>
">L&ouml;schen</a></li>
	<?php endforeach; endif; unset($_from); ?>
</ul>
<?php endif; ?></div>

<br /> -->

<input type="submit" value="Speichern" name="save" /> <input
	type="submit" value="Speichern und Schliessen" name="saveandclose" /> <input
	type="submit" value="Zur&uuml;cksetzen" name="reset" /></form>