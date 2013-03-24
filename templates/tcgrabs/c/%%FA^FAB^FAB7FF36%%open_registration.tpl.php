<?php /* Smarty version 2.6.18, created on 2011-04-09 14:29:37
         compiled from open_registration.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cssmodulo', 'open_registration.tpl', 13, false),array('function', 'html_select_date', 'open_registration.tpl', 57, false),)), $this); ?>
<h1>Anmeldung</h1>
<p>Du kannst dich direkt &uuml;ber unsere Webseite beim Tennisclub Grabs
anmelden. Bitte w&auml;hle das passende Abo aus der folgenden Tabelle
aus und sende deine Anmeldung &uuml;ber das Formular zu uns. Wir werden
dir nach der Anmeldung weitere Infos zukommen lassen.</p>
<br />
<table id="basetabelle">
	<tr>
		<th>Abo</th>
		<th>Beschreibung</th>
		<th>Preis</th>
	</tr>
	<?php $_from = $this->_tpl_vars['module']['abos']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_1739f7c0a0c6e4792dc494d2e7801208):
        $this->_tpl_vars['abo'] = $_1739f7c0a0c6e4792dc494d2e7801208
?> <?php echo smarty_function_cssmodulo(array('class' => 'modulo','varname' => 'tmp'), $this);?>

	<tr>
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><?php echo $this->_tpl_vars['abo']['Name']; ?>
</td>
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><?php echo $this->_tpl_vars['abo']['Description']; ?>
</td>
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><?php echo $this->_tpl_vars['abo']['Preis']; ?>
.-</td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
</table>
<br />
<div id="contentbox">
<h2>Anmeldung</h2>
<form method="POST" action="<?php echo $this->_tpl_vars['module']['link']; ?>
"><label class="reglab"
	for="regdata[name]">Name:</label> <input type="text"
	name="regdata[name]" class="reginput" /><br />

<label class="reglab" for="regdata[firstname]">Vorname:</label> <input
	type="text" name="regdata[firstname]" class="reginput" /><br />

<label class="reglab" for="regdata[adress]">Adresse:</label> <input
	type="text" name="regdata[adress]" class="reginput" /><br />

<label class="reglab" for="regdata[zip]">Plz/Ort:</label> <input
	type="text" name="regdata[zip]" class="reginputzip" /><input
	type="text" name="regdata[place]" class="reginputplace" /><br />

<label class="reglab" for="regdata[email]">E-Mail:</label> <input
	type="text" name="regdata[email]" class="reginput" /><br />


<label class="reglab" for="regdata[phoneprivate]">Telefon (Privat):</label> <input
	type="text" name="regdata[phoneprivate]" class="reginput" /><br />


<label class="reglab" for="regdata[phonegesch]">Telefon (Gesch.):</label> <input
	type="text" name="regdata[phonegesch]" class="reginput" /><br />

<label class="reglab" for="regdata[phonemobile]">Mobile:</label> <input
	type="text" name="regdata[phonemobile]" class="reginput" /><br />

<label class="reglab" for="regdata[phone]">Nationalit&auml;t:</label> <input
	type="text" name="regdata[national]" class="reginput" /><br />

<label class="reglab" for="regdata[phone]">Geburtstag:</label> 
		<?php echo smarty_function_html_select_date(array('prefix' => "",'field_array' => 'regdata','time' => $this->_tpl_vars['module']['termin']['begin'],'display_months' => false,'display_years' => false), $this);?>

	<?php echo smarty_function_html_select_date(array('prefix' => "",'field_array' => 'regdata','time' => $this->_tpl_vars['module']['termin']['begin'],'start_year' => "-90",'display_days' => false), $this);?>

<br />

<label class="reglab" for="regdata[type]">Abo:</label> <select
	name="regdata[abo]" class="reginputsel">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_select.tpl", 'smarty_include_vars' => array('selectOptions' => $this->_tpl_vars['module']['abosSelect'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</select><br />

<label class="reglab" for="regdata[spam]">Spamschutz: 7 + 1 =</label><input
	type="text" name="regdata[spam]" class="reginput" /><br />

<label class="reglab">&nbsp;</label> <input type="submit" name="send"
	value="Anmeldung abschicken" /></form>
</div>