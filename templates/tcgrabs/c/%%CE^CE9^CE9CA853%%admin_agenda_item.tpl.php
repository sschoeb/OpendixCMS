<?php /* Smarty version 2.6.18, created on 2011-04-09 19:51:54
         compiled from admin_agenda_item.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_select_date', 'admin_agenda_item.tpl', 73, false),array('function', 'html_select_time', 'admin_agenda_item.tpl', 75, false),)), $this); ?>
<h1>Administration - Agenda - Element</h1>
<form action="<?php echo $this->_tpl_vars['cms']['link']['save']; ?>
" method="post" enctype="multipart/form-data">

<div id="contentbox">
<h2>Allgemein</h2>
<table>
	<tr>
		<td width="150px">Aktiv</td>
		<td><input type="checkbox" name="termin[active]" <?php echo $this->_tpl_vars['module']['termin']['active']; ?>
 />
		</td>
	</tr>
	<tr>
		<td>Status</td>
		<td><select name="termin[state]">
			<option value="2"<?php echo $this->_tpl_vars['module']['termin']['state']['before']; ?>
>Bevorstehend</option>
			<option value="1"<?php echo $this->_tpl_vars['module']['termin']['state']['finished']; ?>
>Durchgef&uuml;hrt</option>
			<option value="3"<?php echo $this->_tpl_vars['module']['termin']['state']['canceled']; ?>
>Abgesagt</option>
		</select></td>
	</tr>
	<tr>
		<td>Gruppe</td>
		<td><select name="termin[agendaId]">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_select.tpl", 'smarty_include_vars' => array('selectOptions' => $this->_tpl_vars['module']['termin']['agenda'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</select></td>
	</tr>
	<tr>
		<td>Titel</td>
		<td><input type="text" name="termin[name]"
			value="<?php echo $this->_tpl_vars['module']['termin']['name']; ?>
" /></td>
	</tr>
	<tr>
		<td>Treffpunkt</td>
		<td><input type="text" name="termin[place]" value="<?php echo $this->_tpl_vars['module']['termin']['place']; ?>
" /></td>
	</tr>
</table>
</div>
<br />

<div id="contentbox">
<h2>Bermerkung</h2>
<table>
	<tr>
		<td>
			<textarea name="opendixckeditor" cols="70" rows=6><?php echo $this->_tpl_vars['module']['termin']['description']; ?>
</textarea>
			
</td>
	</tr>
</table>
</div>
<br />

<div id="contentbox">
<h2>Kontaktperson</h2>
<table>
	<tr>
		<td width="150px">Name/Vorname</td>
		<td><select name="termin[contactId]">
			<option value="NULL"></option>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_select.tpl", 'smarty_include_vars' => array('selectOptions' => $this->_tpl_vars['module']['termin']['kontakt'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</select></td>
	</tr>
</table>
</div>

<br>

<div id="contentbox">
<h2>Zeit</h2>
<table>
	<tr>
		<td width="150px">Beginn</td>
		<td>
		<?php echo smarty_function_html_select_date(array('prefix' => "",'field_array' => "termin[time][begin]",'time' => $this->_tpl_vars['module']['termin']['begin'],'display_months' => false,'display_years' => false), $this);?>

	<?php echo smarty_function_html_select_date(array('prefix' => "",'field_array' => "termin[time][begin]",'time' => $this->_tpl_vars['module']['termin']['begin'],'end_year' => "+5",'display_days' => false), $this);?>

	<?php echo smarty_function_html_select_time(array('prefix' => "",'field_array' => "termin[time][begin]",'use_24_hours' => true,'display_seconds' => false,'time' => $this->_tpl_vars['module']['termin']['begin']), $this);?>

</td>
	</tr>
	<tr>
		<td>Ende</td>
		<td>
		<?php echo smarty_function_html_select_date(array('prefix' => "",'field_array' => "termin[time][end]",'time' => $this->_tpl_vars['module']['termin']['end'],'display_months' => false,'display_years' => false), $this);?>

	<?php echo smarty_function_html_select_date(array('prefix' => "",'field_array' => "termin[time][end]",'time' => $this->_tpl_vars['module']['termin']['end'],'end_year' => "+5",'display_days' => false), $this);?>

	<?php echo smarty_function_html_select_time(array('prefix' => "",'field_array' => "termin[time][end]",'use_24_hours' => true,'display_seconds' => false,'time' => $this->_tpl_vars['module']['termin']['end']), $this);?>

</tr>
	<tr>
		<td></td>
		<td><input type="checkbox" name="openEnd"<?php echo $this->_tpl_vars['module']['termin']['openEnd']; ?>
>
		Offenes Ende</td>
	</tr>
</table>
</div>
<!-- 
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_timer.tpl", 'smarty_include_vars' => array('timerdata' => $this->_tpl_vars['module']['termin']['timer'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> <br />
 
<!-- 
<div id="contentbox">
<h2>Anmeldung</h2>
<table>
	<tr>
		<td>Anmeldeschluss</td>
		<td><?php echo smarty_function_html_select_date(array('prefix' => 'anmeldung_schluss','time' => $this->_tpl_vars['termin']['anmeldung_schluss'],'display_months' => false,'display_years' => false), $this);?>
 <?php echo smarty_function_html_select_date(array('prefix' => 'anmeldung_schluss','time' => $this->_tpl_vars['termin']['anmeldung_schluss'],'end_year' => "+5",'display_days' => false), $this);?>


		<?php echo smarty_function_html_select_time(array('prefix' => 'anmeldung_schluss','use_24_hours' => true,'display_seconds' => false,'time' => $this->_tpl_vars['termin']['anmeldung_schluss']), $this);?>
</td>
	</tr>
	<tr>
		<td style="border: none;"><input type="checkbox"
			name="termin[anmeldung_moeglich]" <?php echo $this->_tpl_vars['termin']['anmeldung_moeglich']; ?>
 /></td>
		<td style="border: none;">Anmeldung m&ouml;glich</td>
	</tr>
	<tr>
		<td style="border: none;"><img
			src="<?php echo $this->_tpl_vars['opendix_medien']; ?>
/bilder/icons/anmeldung.gif" /></td>
		<td align="left" valign="middle" style="border: none;"><a
			href="<?php echo $this->_tpl_vars['termin']['link']['editanmeldung']; ?>
">Anmeldeformular bearbeiten</a></td>
	</tr>
	<tr>
		<td style="border: none;"><input type="checkbox"
			name="termin[anmeldung_einsehen]" <?php echo $this->_tpl_vars['termin']['anmeldung_einsehen']; ?>
 /></td>
		<td style="border: none;">Angemeldete einsehen</td>
	</tr>
	<tr>
		<td style="border: none;"><img
			src="<?php echo $this->_tpl_vars['opendix_medien']; ?>
/bilder/icons/anmeldung.gif" /></td>
		<td align="left" valign="middle" style="border: none;"><?php if ($this->_tpl_vars['termin']['anzanmeldungen'] > 0): ?> <a href="<?php echo $this->_tpl_vars['termin']['link']['anmeldungen']; ?>
">Anmeldungen
		(<?php echo $this->_tpl_vars['termin']['anzanmeldungen']; ?>
)</a> <?php else: ?> Anmeldungen
		(<?php echo $this->_tpl_vars['termin']['anzanmeldungen']; ?>
) <?php endif; ?></td>
	</tr>
</table>
</div>
<br /> -->
<div id="contentbox">
<h2>Bericht</h2>

<table>
	<tr>
		<td width="150px">Berich Men&uuml;punkt:</td>
		<td><select id="berichtmenueid" name="termin[berichtMenueId]"
	onchange="javascript:ChangeBerichtMenueId();">
	<option>Keine Verlinkung vorhanden</option>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_select.tpl", 'smarty_include_vars' => array('selectOptions' => $this->_tpl_vars['module']['termin']['berichtmenueid'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

		</select></td>
	</tr>
	<tr id="berichtIdLabel">
		<td width="150px">Angeh&auml;ngter Bericht:</td>
		<td><select id="berichtid" name="termin[berichtId]">
			<?php echo $this->_tpl_vars['modul']['termin']['berichtId']; ?>


		</select></td>
	</tr>
</table>
</div>
<br />

<div id="contentbox">
<h2>Anh&auml;nge</h2>
Dateien aus der Filedatenbank hinzuf&uuml;gen:
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_fbbrowser.tpl", 'smarty_include_vars' => array('function' => 'takeAgendaFile')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="selectedFiles" style="display:none;">
 <br />
	<h3>Hinzugef&uuml;gte Dateien:</h3>
	<table id="selectedFilesTable">

	</table>
</div>

<?php if ($this->_tpl_vars['module']['termin']['attachement'] != ''): ?>
<h3>Verlinkte Anh&auml;nge</h3>
<ul>
	<?php $_from = $this->_tpl_vars['module']['termin']['attachement']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_ddbc3c54dcad2801bbee817df94e83c6):
        $this->_tpl_vars['attachement'] = $_ddbc3c54dcad2801bbee817df94e83c6
?>
	<li>
			<a href="<?php echo $this->_tpl_vars['attachement']['link']['get']; ?>
"><?php echo $this->_tpl_vars['attachement']['name']; ?>
</a>
			<a href="<?php echo $this->_tpl_vars['attachement']['link']['delete']; ?>
">L&ouml;schen</a>
	</li>
	<?php endforeach; endif; unset($_from); ?>
</ul>

<?php endif; ?>
</div>
<!-- 
<table>
	<tr>
		<td colspan="2">Dateien aus der Filedatenbank
		hinzuf&uuml;gend&nbsp;&nbsp;&nbsp; <input type="button"
			id="fileBrowserButton" value="Filebrowser anzeigen"
			onclick="javascript:switchFileBrowser();"><br />
		<div id="fileTest"></div>
		</td>
	</tr>
	<tr id="newFileRow" style="display: none;">
		<td valign="top">Ausgew&auml;hlte Dateien:</td>
		<td id="newFileCol"></td>
	</tr>
	<?php if ($this->_tpl_vars['modul']['termin']['anhang'] != ''): ?>
	<tr>
		<td colspan="2">Verlinkte Anhänge:</td>
	</tr>
	<?php $_from = $this->_tpl_vars['modul']['termin']['anhang']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_0c466665c502997399d275db9794b1dc):
        $this->_tpl_vars['anhang'] = $_0c466665c502997399d275db9794b1dc
?>
	<tr>
		<td colspan="2" style="border: none;"><a href="<?php echo $this->_tpl_vars['anhang']['link']['del']; ?>
"><img
			src="<?php echo $this->_tpl_vars['opendix_medien']; ?>
/bilder/icons/x.png" border="0" /></a> <a
			href="<?php echo $this->_tpl_vars['anhang']['link']['get']; ?>
"><?php echo $this->_tpl_vars['modul']['anhang']['name']; ?>
</a></td>
	</tr>
	<?php endforeach; endif; unset($_from); ?> <?php endif; ?>
</table>
 -->

<br />
<input type="submit" name="save" value="Speichern" /> <input
	type="submit" name="saveandclose" value="Speichern und Schliessen" /> <input
	type="submit" name="cancel" value="Abbrechen" /> <input type="reset"
	name="reset" value="Reset" /></form>