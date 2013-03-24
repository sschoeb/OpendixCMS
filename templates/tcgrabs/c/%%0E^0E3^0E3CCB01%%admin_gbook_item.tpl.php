<?php /* Smarty version 2.6.18, created on 2010-07-03 11:47:00
         compiled from admin_gbook_item.tpl */ ?>
<h1>Administration - G&auml;stebuch - Element</h1>
<form action="<?php echo $this->_tpl_vars['cms']['link']['save']; ?>
" method="post">
<div id="contentbox">
<h2>Allgemeine Infos</h2>
<table>
	<tr>
		<td style="width:100px">Erstellt von:</td>
		<td><?php echo $this->_tpl_vars['module']['daten']['name']; ?>
 (<?php echo $this->_tpl_vars['module']['daten']['ip']; ?>
)</td>
	</tr>
	<tr>
		<td>Erstellt am:</td>
		<td><?php echo $this->_tpl_vars['module']['daten']['date']; ?>
</td>
	</tr>
</table>
</div>
<br />
<div id="contentbox">
<h2>Angaben</h2>
<table>
	<tr>
		<td style="width:100px">Name:</td>
		<td><input type="text" size="40" value="<?php echo $this->_tpl_vars['module']['daten']['name']; ?>
" name="name"></td>
	</tr>
	<tr>
		<td >E-Mail:</td>
		<td><input type="text" size="40" value="<?php echo $this->_tpl_vars['module']['daten']['mail']; ?>
" name="mail"></td>
	</tr>
	<!-- 
	<tr>
		<td>Homepage:</td>
		<td><input type="text" size="40" value="<?php echo $this->_tpl_vars['module']['daten']['homepage']; ?>
" name="homepage">
		</td>
	</tr>
	 -->
</table>
</div>
<br />

<div id="contentbox">
<h2>Eintrag</h2>
<table>
	<tr>
		<td><textarea rows="10" cols="81" name="eintrag"><?php echo $this->_tpl_vars['module']['daten']['eintrag']; ?>
</textarea>
		</td>
	</tr>
</table>
</div>
<br />
<input type="submit" name="save" value="Speichern" /> <input
	type="submit" name="saveandclose" value="Speichern und Schliessen" /> <input
	type="submit" name="cancel" value="Abbrechen" /> <input type="reset"
	name="reset" value="Reset" /></form>