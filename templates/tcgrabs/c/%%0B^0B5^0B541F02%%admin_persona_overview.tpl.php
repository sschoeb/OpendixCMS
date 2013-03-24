<?php /* Smarty version 2.6.18, created on 2011-04-03 10:28:57
         compiled from admin_persona_overview.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cssmodulo', 'admin_persona_overview.tpl', 20, false),)), $this); ?>
<h1>Administration - Personen</h1>
<div id="contentbox">
<h2>Neue Person hinzuf&uuml;gen</h2>
<form method="POST" action="<?php echo $this->_tpl_vars['cms']['link']['add']; ?>
">Vorname: <input type="text"
	name="firstname" /> 
	Nachname: <input type="text"
	name="name" /><input type="submit" value="Hinzuf&uuml;gen" /></form>
</div>
<br />

<table id="basetabelle">
	<tr>
		<th style="width:99%">Name</th>
		<!-- <th>Aktiv</th> -->
		<th></th>
	
	</tr>
	<?php $_from = $this->_tpl_vars['module']['personen']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_8b0a44048f58988b486bdd0d245b22a8):
        $this->_tpl_vars['person'] = $_8b0a44048f58988b486bdd0d245b22a8
?>
	<tr>
		<?php echo smarty_function_cssmodulo(array('class' => 'modulo','varname' => 'tmp'), $this);?>

		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><a href="<?php echo $this->_tpl_vars['person']['link']['edit']; ?>
"><?php echo $this->_tpl_vars['person']['name']; ?>
</a></td>
		<!-- <td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><a href="<?php echo $this->_tpl_vars['news']['link']['switchactive']; ?>
">Switchactive</a></td> -->
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><?php if (! $this->_tpl_vars['person']['nodelete']): ?><a href="<?php echo $this->_tpl_vars['person']['link']['delete']; ?>
"><img src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/delete.png" alt="L&ouml;schen" /></img></a><?php endif; ?></td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
</table>