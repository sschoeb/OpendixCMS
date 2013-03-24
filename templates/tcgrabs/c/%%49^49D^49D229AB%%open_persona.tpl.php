<?php /* Smarty version 2.6.18, created on 2011-04-03 09:01:49
         compiled from open_persona.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'mailto', 'open_persona.tpl', 28, false),)), $this); ?>

<h1><?php echo $this->_tpl_vars['module']['title']; ?>
</h1>

<?php $_from = $this->_tpl_vars['module']['persona']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_8b0a44048f58988b486bdd0d245b22a8):
        $this->_tpl_vars['person'] = $_8b0a44048f58988b486bdd0d245b22a8
?>

<div class="person">
<div class="left">
<div class="foto"
	style="background: #fff url('<?php echo $this->_tpl_vars['person']['bild']; ?>
') 50% 50% no-repeat"></div>
</div>
<div class="right">
<h2><?php echo $this->_tpl_vars['person']['funktion']; ?>
</h2>
<table>
	<tr>
		<td width="130">Name</td>
		<td><?php echo $this->_tpl_vars['person']['firstname']; ?>
 <?php echo $this->_tpl_vars['person']['name']; ?>
</td>
	</tr>
	<tr>
		<td>Adresse</td>
		<td><?php echo $this->_tpl_vars['person']['street']; ?>
</td>
	</tr>
	<tr>
		<td>Ort</td>
		<td><?php echo $this->_tpl_vars['person']['zip']; ?>
 <?php echo $this->_tpl_vars['person']['residence']; ?>
</td>
	</tr>
	<tr>
		<td>E-Mail</td>
		<td><a href="mailto:"><?php echo smarty_function_mailto(array('address' => $this->_tpl_vars['person']['email'],'encode' => 'javascript'), $this);?>
</a></td>
	</tr>
	<?php if ($this->_tpl_vars['person']['phoneprivate'] != ''): ?>
	<tr>
		<td>Telefon (Privat)</td>
		<td><?php echo $this->_tpl_vars['person']['phoneprivate']; ?>
</td>
	</tr>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['person']['phonebusiness'] != ''): ?>
	 <tr>
		<td>Telefon (Gesch&auml;ft)</td>
		<td><?php echo $this->_tpl_vars['person']['phonebusiness']; ?>
</td>
	</tr>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['person']['phonemobile'] != ''): ?>
	<tr>
		<td>Telefon (Mobil)</td>
		<td><?php echo $this->_tpl_vars['person']['phonemobile']; ?>
</td>
	</tr> 
	<?php endif; ?>
</table>
</div>
<div class="clear"></div>
</div>
<?php endforeach; endif; unset($_from); ?>