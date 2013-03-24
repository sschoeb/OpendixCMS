<?php /* Smarty version 2.6.18, created on 2011-04-06 18:18:11
         compiled from admin_galerie_item.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_select_date', 'admin_galerie_item.tpl', 13, false),)), $this); ?>
<form method="POST" action="<?php echo $this->_tpl_vars['cms']['link']['save']; ?>
">
<div id="contentbox">
<h2>Allgemein</h2>
<table>

	<tr>
		<td>Name</td>
		<td><input type="text" name="galerie[name]" value="<?php echo $this->_tpl_vars['module']['galerie']['name']; ?>
"
			size="50" /></td>
	</tr>
	<tr>
		<td>Datum</td>
		<td><?php echo smarty_function_html_select_date(array('field_array' => "galerie[date]",'prefix' => "",'time' => $this->_tpl_vars['module']['galerie']['date'],'display_months' => false,'display_years' => false), $this);?>
 <?php echo smarty_function_html_select_date(array('prefix' => "",'field_array' => "galerie[date]",'time' => $this->_tpl_vars['module']['galerie']['date'],'start_year' => "-10",'end_year' => "+10",'display_days' => false), $this);?>
</td>
	</tr>
	<tr>
		<td valign="top">Bemerkung</td>
		<td><textarea name="galerie[description]" cols="50" rows="4"><?php echo $this->_tpl_vars['module']['galerie']['description']; ?>
</textarea>
		</td>
	</tr>
	<tr>
		<td></td>
		<td><input type="checkbox" name="galerie[active]" <?php echo $this->_tpl_vars['module']['galerie']['active']; ?>
 />
		Aktiv</td>
	</tr>
</table>
</div>
<br />
<div id="contentbox">
<h2>Photograf</h2>
<table align="center" width="90%">

	<tr>
		<td width="25px"><input type="radio" name="galerie[isUser]" value="1" <?php echo $this->_tpl_vars['module']['galerie']['isUser']['known']; ?>
 /></td>
		<td>Bekannter Benutzer</td>
	</tr>
	<tr>
		<td></td>
		<td>Name
		<select name="galerie[knownPhotograf]">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_select.tpl", 'smarty_include_vars' => array('selectOptions' => $this->_tpl_vars['module']['galerie']['user'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			
		</select>
		</td>
	</tr>
	<tr>
		<td><input type="radio" name="galerie[isUser]" value="0" <?php echo $this->_tpl_vars['module']['galerie']['isUser']['unknown']; ?>
 />
		</td>
		<td>Unbekannter Benutzer</td>
	</tr>
	<tr>
		<td></td>
		<td>Name<input type="text" name="galerie[photograf]" value="<?php echo $this->_tpl_vars['module']['galerie']['photograf']; ?>
"></td>
	</tr>
</table>
</div>
<br />
<div id="contentbox">
<h2>Hinzugef&uuml;gte Bilder</h2>
<table>
	<?php $_from = $this->_tpl_vars['module']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_b798abe6e1b1318ee36b0dcb3fb9e4d3):
        $this->_tpl_vars['img'] = $_b798abe6e1b1318ee36b0dcb3fb9e4d3
?>
	<tr>
		<td><?php echo $this->_tpl_vars['img']['path']['image']; ?>
</td>
		<td><a href="<?php echo $this->_tpl_vars['img']['path']['delete']; ?>
"><img
			src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
delete.png" /></a></td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
</table>
</div>
<br />
<div id="contentbox">
<h2>Bilder hinzuf&uuml;gen</h2>
<?php if ($this->_tpl_vars['module']['galerie']['importImgCount'] == 0): ?>
<div id="noimages">Es befinden sich keine Bilder im Import-Ordner. Kein
Import m&ouml;glich.</div>
<?php else: ?>
<div id="imagesAvailable">Es befinden sich momentan <span id="imgCount"><?php echo $this->_tpl_vars['module']['galerie']['importImgCount']; ?>
</span>
Bilder im Import-Ordner. Klicken Sie auf den folgenden Button um den
Import zu starten
<input type="button" id="impButton" onclick="javascript:start(<?php echo $this->_tpl_vars['module']['galerie']['id']; ?>
, <?php echo $this->_tpl_vars['module']['galerie']['importImgCount']; ?>
);" value="Import starten" />
</div>
<ul class="dottedlist" id="impList">

</ul>

<?php endif; ?></div>
<br />
<input type="submit" name="save" value="Speichern" /> <input
	type="submit" name="saveandclose" value="Speichern und Schliessen" /> <input
	type="submit" name="cancel" value="Abbrechen" /> <input type="reset"
	name="reset" value="Reset" /></form>