<?php /* Smarty version 2.6.18, created on 2011-04-03 08:55:58
         compiled from open_profile.tpl */ ?>
 <?php if ($this->_tpl_vars['module']['action'] == 'login'): ?>
<h1>Login</h1>
<form method="post" action="<?php echo $this->_tpl_vars['module']['loginlink']; ?>
">
<div id="contentbox">
<table>
	<tr>
		<td style="width: 120px">Benutzername:</td>
		<td><input type="text" name="username" /></td>
	</tr>
	<tr>
		<td>Passwort:</td>
		<td><input type="password" name="password" /></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" value="Login" /></td>
	</tr>
</table>
</div>
</form>
<?php else: ?>
<h1>Profil von <?php echo $this->_tpl_vars['module']['user']['nick']; ?>
</h1>
<form method="post" action="<?php echo $this->_tpl_vars['cms']['link']['save']; ?>
">
<div id="contentbox">
<h2>Kontakt</h2>
<table>
	<tr>
		<td  style="width: 200px">Vorname:</td>
		<td><input type="text" name="user[firstname]" value="<?php echo $this->_tpl_vars['module']['user']['firstname']; ?>
" size=27 /></td>
	</tr>
	<tr>
		<td>Nachname:</td>
		<td><input type="text" name="user[name]" value="<?php echo $this->_tpl_vars['module']['user']['name']; ?>
"  size=27/></td>
	</tr>
	<tr>
		<td>E-Mail:</td>
		<td><input type="text" name="user[email]" value="<?php echo $this->_tpl_vars['module']['user']['email']; ?>
"  size=27/></td>
	</tr>
	<tr>
		<td>Telefon (Privat):</td>
		<td><input type="text" name="user[phoneprivate]" value="<?php echo $this->_tpl_vars['module']['user']['phoneprivate']; ?>
" size=27 /></td>
	</tr>
	<tr>
		<td>Telefon (Gesch&auml;ft):</td>
		<td><input type="text" name="user[phonebusiness]" value="<?php echo $this->_tpl_vars['module']['user']['phonebusiness']; ?>
" size=27 /></td>
	</tr>
	<tr>
		<td>Telfon (Mobile):</td>
		<td><input type="text" name="user[phonemobile]" value="<?php echo $this->_tpl_vars['module']['user']['phonemobile']; ?>
" size=27 /></td>
	</tr>
	<tr>
		<td>Adresse:</td>
		<td><input type="text" name="user[street]" value="<?php echo $this->_tpl_vars['module']['user']['street']; ?>
" size=27 /></td>
	</tr>
	<tr>
		<td>Plz/Ort:</td>
		<td><input type="text" name="user[zip]" value="<?php echo $this->_tpl_vars['module']['user']['zip']; ?>
" size=4 />
		<input type="text" name="user[residence]" value="<?php echo $this->_tpl_vars['module']['user']['residence']; ?>
" /></td>
	</tr>
</table>
</div>
<br />

<div id="contentbox">
<h2>System</h2>
<table>
	<tr>
		<td   style="width: 200px">Template:</td>
		<td><select name="user[template]"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_select.tpl", 'smarty_include_vars' => array('selectOptions' => $this->_tpl_vars['module']['template'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></select></td>
	</tr>
</table>
</div>
<br />
<div id="contentbox">
<h2>Passwort</h2>
<table>
	<tr>
		<td  style="width: 200px">Altes Passwort:</td>
		<td><input type="password" name="user[oldpassword]" /></td>
	</tr>
	<tr>
		<td>Neues Passwort:</td>
		<td><input type="password" name="user[newpassword]" /></td>
	</tr>
	<tr>
		<td>Neues Passwort(Best&auml;tigung):</td>
		<td><input type="password" name="user[newpasswordconf]" /></td>
	</tr>
</table>
</div>
<br />
<input type="submit" value="Speichern" />
</form>
<?php endif; ?>