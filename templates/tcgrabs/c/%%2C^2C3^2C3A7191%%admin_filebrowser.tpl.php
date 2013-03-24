<?php /* Smarty version 2.6.18, created on 2011-04-06 18:08:49
         compiled from admin_filebrowser.tpl */ ?>
<h1>Dateibrowser</h1>
<form method="POST" action="<?php echo $this->_tpl_vars['module']['uploadLink']; ?>
"
		enctype="multipart/form-data" style="display: inline;">
<div id="contentbox">
<h2>Dateiupload</h2>
<input type="file" name="fileUpload"><input type="submit" value="Upload">
</div>
</form>
<br />
<div id="contentbox">
<h2>Dateibrowser</h2>
<a href="<?php echo $this->_tpl_vars['module']['rootLink']; ?>
">Root /</a> <?php $_from = $this->_tpl_vars['module']['filebase_navLink']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_80e2dde63efb97e07ea27235d6b108f1):
        $this->_tpl_vars['navItem'] = $_80e2dde63efb97e07ea27235d6b108f1
?> <a href="<?php echo $this->_tpl_vars['navItem']['link']; ?>
"><?php echo $this->_tpl_vars['navItem']['name']; ?>
</a>
<?php endforeach; endif; unset($_from); ?>
<h3>Dateisystem</h3>
<div id="fbbrowser">
<table style="width:575px;"">
	<?php if ($this->_tpl_vars['module']['filesBackLink'] != ''): ?>
	<tr class="filerow">
		<td colspan="2"><a href="<?php echo $this->_tpl_vars['module']['filesBackLink']; ?>
"><img class="fileimg"
			src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/dirup.png" border="0" />..</a></td>
	</tr>
	<?php endif; ?> 

	<?php if (count ( $this->_tpl_vars['module']['directories'] ) == 0 && count ( $this->_tpl_vars['module']['files'] ) == 0): ?>
		<tr><td>Dieser Ordner ist leer</td></tr>
	<?php endif; ?>

	<?php $_from = $this->_tpl_vars['module']['directories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_736007832d2167baaae763fd3a3f3cf1):
        $this->_tpl_vars['dir'] = $_736007832d2167baaae763fd3a3f3cf1
?>
	<tr class="filerow">
		<td colspan="2"><a href="<?php echo $this->_tpl_vars['dir']['link']; ?>
"><img class="fileimg"
			src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/dir.png"><?php echo $this->_tpl_vars['dir']['name']; ?>
</a></td>
	</tr>
	<?php endforeach; endif; unset($_from); ?> <?php $_from = $this->_tpl_vars['module']['files']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_8c7dd922ad47494fc02c388e12c00eac):
        $this->_tpl_vars['file'] = $_8c7dd922ad47494fc02c388e12c00eac
?>
	<tr class="filerow">
		<td><img src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/file.gif" class="fileimg"><?php echo $this->_tpl_vars['file']['name']; ?>
</a></td>
		<td width="5%"><a href="<?php echo $this->_tpl_vars['file']['link']['del']; ?>
"><img
			src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/delete.png"></a></td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
</table>
</div>

<h3>Ordner erstellen</h3>
<form action="<?php echo $this->_tpl_vars['module']['createFolderLink']; ?>
" style="display: inline;"
			method="POST">Neuen Ordner erstellen: <input type="text"
			name="newFolder" /> <input type="submit" value="Erstellen"></form>
</div>

<table width="90%" align="center">
	
	<tr>
		<td></td>
	</tr>
	<tr>
		<td></td>
	</tr>
	
	<tr>
		<td>
		
		</td>
	</tr>
</table>