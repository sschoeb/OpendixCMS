<?php /* Smarty version 2.6.18, created on 2011-04-08 10:42:07
         compiled from admin_registration.tpl */ ?>
<form method="post" action="<?php echo $this->_tpl_vars['cms']['link']['save']; ?>
">
<h1>Administration - Registration</h1>
<div id="contentbox">
<h2>Text Benutzer</h2>
<textarea name="textuser" rows="10" cols="80">
<?php echo $this->_tpl_vars['module']['data']['userText']; ?>

</textarea>
</div>
<br />
<div id="contentbox">
<h2>Admin</h2>
E-Mail:
<input type="text" name="mailadmin" value="<?php echo $this->_tpl_vars['module']['data']['adminMail']; ?>
"/>
<br />
Text:<br/>
<textarea name="textadmin"  rows="14" cols="80">
<?php echo $this->_tpl_vars['module']['data']['adminText']; ?>

</textarea>

Verf&uuml;gbare Tags: <br />[NAME], [FIRSTNAME], [ADRESS], [ZIP], [PLACE], [EMAIL], [PHONEPRIVATE], [PHONEGESCH], [PHONEMOBILE], [ABO],  [DAY], [MONTH], [YEAR], [NATIONAL]
</div>
<br />
<input type="submit" name="save" value="Speichern" />
</form>