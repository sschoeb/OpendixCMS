<?php /* Smarty version 2.6.18, created on 2012-02-08 18:14:12
         compiled from admin_persona_item.tpl */ ?>
<h1>Administration - Personen - Element</h1>


<form method="post" action="<?php echo $this->_tpl_vars['cms']['link']['save']; ?>
"
	enctype="multipart/form-data">
<div id="contentbox">
<h2>Kontakt</h2>
<table>
	<tr>
		<td style="width: 160px">Vorname:</td>
		<td><input type="text" name="user[firstname]"
			value="<?php echo $this->_tpl_vars['module']['user']['firstname']; ?>
" size=27 /></td>
	</tr>
	<tr>
		<td>Nachname:</td>
		<td><input type="text" name="user[name]" value="<?php echo $this->_tpl_vars['module']['user']['name']; ?>
"
			size=27 /></td>
	</tr>
	<tr>
		<td>E-Mail:</td>
		<td><input type="text" name="user[email]" value="<?php echo $this->_tpl_vars['module']['user']['email']; ?>
"
			size=27 /></td>
	</tr>
	<tr>
		<td>Telefon (Privat):</td>
		<td><input type="text" name="user[phoneprivate]"
			value="<?php echo $this->_tpl_vars['module']['user']['phoneprivate']; ?>
" size=27 /></td>
	</tr>
	<tr>
		<td>Telefon (Gesch&auml;ft):</td>
		<td><input type="text" name="user[phonebusiness]"
			value="<?php echo $this->_tpl_vars['module']['user']['phonebusiness']; ?>
" size=27 /></td>
	</tr>
	<tr>
		<td>Telfon (Mobile):</td>
		<td><input type="text" name="user[phonemobile]"
			value="<?php echo $this->_tpl_vars['module']['user']['phonemobile']; ?>
" size=27 /></td>
	</tr>
	<tr>
		<td>Adresse:</td>
		<td><input type="text" name="user[street]"
			value="<?php echo $this->_tpl_vars['module']['user']['street']; ?>
" size=27 /></td>
	</tr>
	<tr>
		<td>Plz/Ort:</td>
		<td><input type="text" name="user[zip]" value="<?php echo $this->_tpl_vars['module']['user']['zip']; ?>
"
			size=4 /> <input type="text" name="user[residence]"
			value="<?php echo $this->_tpl_vars['module']['user']['residence']; ?>
" /></td>
	</tr>
</table>
</div>

<br />

<div class="person">
	<div class="left">
		<div class="foto" style="background: #fff "><?php if ($this->_tpl_vars['module']['user']['image'] != ''): ?><img src="<?php echo $this->_tpl_vars['module']['user']['image']; ?>
"/><?php endif; ?></div>
	</div>
	
	<div class="right">
		<h2>Anzeigebild</h2>
		<p>Neues Bild uploaden: </p>
		<input type="file" name="newImage" />
	</div>
	
	<div class="clear"></div>
</div>

<br />

<div id="contentbox">
<h2>Gruppenangeh&ouml;rigkeiten</h2>
<?php $_from = $this->_tpl_vars['module']['usergroupsPers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_db0f6f37ebeb6ea09489124345af2a45):
        $this->_tpl_vars['group'] = $_db0f6f37ebeb6ea09489124345af2a45
?> <?php echo $this->_tpl_vars['group']['name']; ?>
<br />
<?php endforeach; endif; unset($_from); ?> <?php $_from = $this->_tpl_vars['module']['usergroupsAdmin']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_db0f6f37ebeb6ea09489124345af2a45):
        $this->_tpl_vars['group'] = $_db0f6f37ebeb6ea09489124345af2a45
?> <font
	color="orange"><?php echo $this->_tpl_vars['group']['name']; ?>
 (System)</font><br />
<?php endforeach; endif; unset($_from); ?></div>

<br />

<input type="submit" name="save" value="Speichern" /> <input
	type="submit" name="saveandclose" value="Speichern und Schliessen" /> <input
	type="submit" name="cancel" value="Abbrechen" /> <input type="reset"
	name="reset" value="Reset" /></form>