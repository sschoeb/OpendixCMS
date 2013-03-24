<?php /* Smarty version 2.6.18, created on 2013-03-12 21:28:15
         compiled from admin_download.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'filesize', 'admin_download.tpl', 45, false),)), $this); ?>
<h1>Downloads verwalten</h1>
<div id="contentbox">
<h2>Downloads hinzuf&uuml;gen</h2>
<h3>Upload</h3>
<form action="<?php echo $this->_tpl_vars['cms']['link']['upload']; ?>
" method="POST"
	enctype="multipart/form-data">
<div class="linkFile">
<table>
	<tr>
		<td>Datei ausw&auml;hlen:</td>
		<td><input type="file" name="file" size="40" /></td>
	</tr>
	<tr>
		<td>Gruppe:</td>
		<td><select name="uploadgroup">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_select.tpl", 'smarty_include_vars' => array('selectOptions' => $this->_tpl_vars['module']['groups'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</select></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" value="Upload" /></td>
	</tr>
</table>
</div>
</form>

<h3>Filebase</h3>
<div class="linkFile">
<form action="<?php echo $this->_tpl_vars['cms']['link']['save']; ?>
" method="POST"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_fbbrowser.tpl", 'smarty_include_vars' => array('function' => 'takeDownloadFile')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="selectedFiles" style="display: none;"><b>Neu verlinkte Dateien:</b>
<br />
<ul id="selectedFilesList">

</ul>
</div>

</div>
</div>
<br />
<div id="contentbox">
<h2>Verf&uuml;gbare Downloads</h2>

<?php $_from = $this->_tpl_vars['module']['downloads']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_fd456406745d816a45cae554c788e754):
        $this->_tpl_vars['download'] = $_fd456406745d816a45cae554c788e754
?> <b><?php echo $this->_tpl_vars['download']['fileId']; ?>

(<?php echo smarty_function_filesize(array('bytes' => $this->_tpl_vars['download']['size']), $this);?>
)</b>&nbsp;<a
	href="<?php echo $this->_tpl_vars['download']['link']['get']; ?>
">Download</a> <br />
<table>
	<?php if ($this->_tpl_vars['download']['fileError']): ?>
	<tr>
		<td colspan="2">DIESE DATEI EXISTIERT NICHT!!</td>
	</tr>
	<?php endif; ?>
	<tr>
		<td width="100px">Beschreibung:</td>
		<td><input type="text" value="<?php echo $this->_tpl_vars['download']['description']; ?>
"
			name="daten[<?php echo $this->_tpl_vars['download']['id']; ?>
][description]" size="60" /></td>
	</tr>
	<tr>
		<td>Alias:</td>
		<td><input type="text" value="<?php echo $this->_tpl_vars['download']['fileAlias']; ?>
"
			name="daten[<?php echo $this->_tpl_vars['download']['id']; ?>
][filealias]<?php echo $this->_tpl_vars['download']['id']; ?>
" size="60" /></td>
	</tr>
	<tr>
		<td>Reihenfolge:</td>
		<td><input type="text" value="<?php echo $this->_tpl_vars['download']['order']; ?>
"
			name="daten[<?php echo $this->_tpl_vars['download']['id']; ?>
][order]<?php echo $this->_tpl_vars['download']['id']; ?>
" size="60" /></td>
	</tr>

	<tr>
		<td>Gruppe:</td>
		<td><select name="daten[<?php echo $this->_tpl_vars['download']['id']; ?>
][group]">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_select.tpl", 'smarty_include_vars' => array('selectOptions' => $this->_tpl_vars['download']['group'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</select></td>
	</tr>
	<tr>
		<td></td>
		<td><a href="<?php echo $this->_tpl_vars['download']['link']['delete']; ?>
"><img
			src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/delete.png" />Loeschen</a></td>
	</tr>

</table>

<hr />
<?php endforeach; endif; unset($_from); ?></div>
<br />
<!-- saveandclose damit von der Modulbase die Übersicht aufgerufen wird und nicht die Item-Methode¨-->
<input type="submit" value="Speichern" name="saveandclose" />
<input type="reset" value="Zur&uuml;cksetzen" name="reset" />

</form>