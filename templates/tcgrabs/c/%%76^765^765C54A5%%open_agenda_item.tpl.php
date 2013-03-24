<?php /* Smarty version 2.6.18, created on 2011-04-09 19:52:10
         compiled from open_agenda_item.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'agendastate', 'open_agenda_item.tpl', 10, false),array('function', 'mailto', 'open_agenda_item.tpl', 79, false),)), $this); ?>

<h1><?php echo $this->_tpl_vars['module']['termin']['name']; ?>
</h1>

<div id="anlassdaten">


<table class="kalender">
	<tr>
		<td width="130">Status</td>
		<td width="480"><?php echo smarty_function_agendastate(array('state' => $this->_tpl_vars['module']['termin']['state']), $this);?>
</td>
	</tr>
	<tr>
		<td>Beginn</td>
		<td><?php echo $this->_tpl_vars['module']['termin']['begin']; ?>
 Uhr</td>
	</tr>
	<tr>
		<td>Ende</td>
		<td><?php echo $this->_tpl_vars['module']['termin']['end']; ?>
 Uhr</td>
	</tr>
	<?php if ($this->_tpl_vars['module']['termin']['place'] != ''): ?>
	<tr>
		<td>Treffpunkt</td>
		<td><?php echo $this->_tpl_vars['module']['termin']['place']; ?>
</td>
	</tr>
	<?php endif; ?> 
	<?php if ($this->_tpl_vars['module']['termin']['description'] != ''): ?>
	<tr>
		<td valign="top">Bemerkung</td>
		<td><?php echo $this->_tpl_vars['module']['termin']['description']; ?>
</td>
	</tr>
	<?php endif; ?>
</table>




<?php if ($this->_tpl_vars['module']['termin']['contact'] != ''): ?>
<hr />
<table class="ansprechpartner">
	<tr>
		<td colspan="2">
		<h2>Kontaktperson / Ansprechpartner</h2>
		</td>
	</tr>
	<tr>
		<td width="130">Name</td>
		<td width=480"><?php echo $this->_tpl_vars['module']['termin']['contact']['details']['firstname']; ?>

		<?php echo $this->_tpl_vars['module']['termin']['contact']['details']['name']; ?>
</td>
	</tr>
	<tr>
		<td>Strasse</td>
		<td><?php echo $this->_tpl_vars['module']['termin']['contact']['details']['street']; ?>
</td>
	</tr>
	<tr>
		<td>Wohnort</td>
		<td><?php echo $this->_tpl_vars['module']['termin']['contact']['details']['zip']; ?>

		<?php echo $this->_tpl_vars['module']['termin']['contact']['details']['residence']; ?>
</td>
	</tr>
	<?php if ($this->_tpl_vars['module']['termin']['contact']['details']['phoneprivate'] != ''): ?>
	<tr>
		<td>Telefon (Privat)</td>
		<td><?php echo $this->_tpl_vars['module']['termin']['contact']['details']['phoneprivate']; ?>
</td>
	</tr>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['module']['termin']['contact']['details']['phonebusiness'] != ''): ?>
	<tr>
		<td>Telefon (Gesch&auml;ft)</td>
		<td><?php echo $this->_tpl_vars['module']['termin']['contact']['details']['phonebusiness']; ?>
 </td>
	</tr>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['module']['termin']['contact']['details']['phonemobile'] != ''): ?>
	<tr>
		<td>Telefon (Mobile)</td>
		<td><?php echo $this->_tpl_vars['module']['termin']['contact']['details']['phonemobile']; ?>
</td>
	</tr>
	<?php endif; ?>
	<tr>
		<td>E-Mail</td>
		<td><?php echo smarty_function_mailto(array('address' => $this->_tpl_vars['module']['termin']['contact']['details']['email'],'encode' => 'javascript'), $this);?>
</td>
	</tr>
</table>
<?php endif; ?>

<?php if (count ( $this->_tpl_vars['module']['termin']['attachements'] ) > 0): ?>
<hr />


<table class="daten">
	<tr>
		<td colspan="2">
		<h2>Dateien zu dieser Veranstaltung</h2>
		</td>
	</tr>
	<tr>
		<td width="100" valign="top">Anh&auml;nge</td>
		<td width="510">
		<?php $_from = $this->_tpl_vars['module']['termin']['attachements']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_915e375d95d78bf040a2e054caadfb56):
        $this->_tpl_vars['attach'] = $_915e375d95d78bf040a2e054caadfb56
?>
			<a href="<?php echo $this->_tpl_vars['attach']['link']['get']; ?>
"><?php echo $this->_tpl_vars['attach']['name']; ?>
</a><br />
		<?php endforeach; endif; unset($_from); ?>
		</td>
	</tr>
	<!-- <tr>
		<td>Galerie</td>
		<td><a href="#">Galerie Clubversmamlung 2009</a></td>
	</tr>
	<tr>
		<td>Berichte</td>
		<td><a href="#">Bericht Clubversammlung 2009</a></td>
	</tr> -->
</table>
<?php endif; ?></div>

<br />
<br />