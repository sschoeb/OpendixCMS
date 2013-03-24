<?php /* Smarty version 2.6.18, created on 2011-08-29 21:14:38
         compiled from admin_agenda.tpl */ ?>
<?php if ($_GET['action'] == 'item' || $_POST['save'] || $_GET['action'] == 'add' || $_GET['action'] == 'delattachement'): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "admin_agenda_item.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>	
<?php else: ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "admin_agenda_overview.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>


<!-- 
<?php if ($_GET['action'] == 'editanmeldung'): ?>
<form action="<?php echo $this->_tpl_vars['saveLink']; ?>
" method="post">
<table align="center" width="90%">
	<tr>
		<td class="boxhead">
		Bemerkung
		</td>
	</tr>
	<tr>
		<td>
		<?php echo $this->_tpl_vars['editor']; ?>

		</td>
	</tr>
</table>

<br />

<table width="90%" align="center">
	<tr>
		<td class="boxhead" colspan="3">
			Formular
		</td>
	</tr>
	<tr class="tabelle_hervorgehoben">
		<td width="10%">
			<b>Anzeigen</b>
		</td>
		<td width="10%">
			<b>Pflicht</b>
		</td>
		<td width="80%">
			<b>Beschreibung</b>
		</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="anmeldeform[vorname]" <?php echo $this->_tpl_vars['agendaform']['vorname']; ?>
 />
		</td>
		<td>
			<input type="checkbox" name="pflicht[vorname]" <?php echo $this->_tpl_vars['agendapflicht']['vorname']; ?>
 />
		</td>
		<td>
			Vorname
		</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="anmeldeform[nachname]" <?php echo $this->_tpl_vars['agendaform']['nachname']; ?>
 />
		</td>
		<td>
			<input type="checkbox" name="pflicht[nachname]" <?php echo $this->_tpl_vars['agendapflicht']['nachname']; ?>
 />
		</td>
		<td>
			Nachname
		</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="anmeldeform[strasse]" <?php echo $this->_tpl_vars['agendaform']['strasse']; ?>
 />
		</td>
		<td>
			<input type="checkbox" name="pflicht[strasse]" <?php echo $this->_tpl_vars['agendapflicht']['strasse']; ?>
 />
		</td>
		<td>
			Strasse
		</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="anmeldeform[wohnort]" <?php echo $this->_tpl_vars['agendaform']['wohnort']; ?>
 />
		</td>
		<td>
			<input type="checkbox" name="pflicht[wohnort]" <?php echo $this->_tpl_vars['agendapflicht']['wohnort']; ?>
 />
		</td>
		<td>
			Wohnort
		</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="anmeldeform[email]" <?php echo $this->_tpl_vars['agendaform']['email']; ?>
 />
		</td>
		<td>
			<input type="checkbox" name="pflicht[email]" <?php echo $this->_tpl_vars['agendapflicht']['email']; ?>
 />
		</td>
		<td>
			E-Mail
		</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="anmeldeform[telefon]" <?php echo $this->_tpl_vars['agendaform']['telefon']; ?>
 />
		</td>
		<td>
			<input type="checkbox" name="pflicht[telefon]" <?php echo $this->_tpl_vars['agendapflicht']['telefon']; ?>
 />
		</td>
		<td>
			Telefon
		</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="anmeldeform[bemerkung]" <?php echo $this->_tpl_vars['agendaform']['bemerkung']; ?>
 />
		</td>
		<td>
			<input type="checkbox" name="pflicht[bemerkung]" <?php echo $this->_tpl_vars['agendapflicht']['bemerkung']; ?>
 />
		</td>
		<td>
			Bemerkung
		</td>
	</tr>
</table>

<br />

<table align="center" width="90%">
	<tr>
		<td class="boxhead" colspan="2">
			Best&auml;tigung
		</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="bes[benutzer]" <?php echo $this->_tpl_vars['agendaform']['best_benutzer']; ?>
 />
		</td>
		<td>
			Anmeldebest&auml;tigung an den Benutzer schicken
		</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="bes[kontakt]" <?php echo $this->_tpl_vars['agendaform']['bes_kontakt']; ?>
 />
		</td>
		<td>
			Anmeldebest&auml;tigung an Kontaktperson senden
		</td>
	</tr>
</table>


<br />

	<div align="center"><input type="submit" name="save" value="Speichern" /></div>
</form>
<?php endif; ?>

<?php if ($this->_tpl_vars['anmeldungen'] != ''): ?>
<div align="center"><b><a href="<?php echo $this->_tpl_vars['zurueckLink']; ?>
">Zur&uuml;ck</a></b></div>
<?php $_from = $this->_tpl_vars['anmeldungen']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_60aac2fb6efd740df1fdb3269802d378):
        $this->_tpl_vars['anmeldung'] = $_60aac2fb6efd740df1fdb3269802d378
?>
		<table align="center" width="90%">
			<tr>
				<td class="boxhead" colspan="2">
					<?php echo $this->_tpl_vars['anmeldung']['vorname']; ?>
 <?php echo $this->_tpl_vars['anmeldung']['nachname']; ?>

				</td>
			</tr>
			<tr>
				<td width="25%">
					Strasse:
				</td>
				<td width="75%">
				<?php if ($this->_tpl_vars['anmeldung']['strasse'] == ''): ?>
					<font color="Red">Keine Strasse angegeben!</font>
				<?php else: ?>
					<?php echo $this->_tpl_vars['anmeldung']['strasse']; ?>

				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>
					Wohnort:
				</td>
				<td>
				<?php if ($this->_tpl_vars['anmeldung']['wohnort'] == ''): ?>
					<font color="Red">Keinen Wohnort angegeben!</font>
				<?php else: ?>
					<?php echo $this->_tpl_vars['anmeldung']['wohnort']; ?>

				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>
					Telefon:
				</td>
				<td>
				<?php if ($this->_tpl_vars['anmeldung']['telefon'] == ''): ?>
					<font color="Red">Keine Telefonnummer angegeben!</font>
				<?php else: ?>
					<?php echo $this->_tpl_vars['anmeldung']['telefon']; ?>

				<?php endif; ?>

				</td>
			</tr>
			<tr>
				<td>
					E-Mail
				</td>
				<td>

				<?php if ($this->_tpl_vars['anmeldung']['email'] == ''): ?>
					<font color="Red">Keine E-Mail angegeben!</font>
				<?php else: ?>
					<a href="mailto:<?php echo $this->_tpl_vars['anmeldung']['email']; ?>
"><?php echo $this->_tpl_vars['anmeldung']['email']; ?>
</a>
				<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td valign="top">
					Bemerkung:
				</td>
				<td>
				<?php if ($this->_tpl_vars['anmeldung']['bemerkung'] == ''): ?>
					<font color="Red">Keine E-Mail angegeben!</font>
				<?php else: ?>
					<?php echo $this->_tpl_vars['anmeldung']['bemerkung']; ?>

				<?php endif; ?>
				</td>
			</tr>
		</table>
		<br />
<?php endforeach; endif; unset($_from); ?>

<div align="center"><b><a href="<?php echo $this->_tpl_vars['zurueckLink']; ?>
">Zur&uuml;ck</a></b></div>
<?php endif; ?> -->