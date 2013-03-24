<?php /* Smarty version 2.6.18, created on 2011-04-03 10:44:09
         compiled from open_bericht_overview.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cssmodulo', 'open_bericht_overview.tpl', 9, false),)), $this); ?>
<h1><?php echo $this->_tpl_vars['module']['title']; ?>
</h1>
<table id="berichttabelle" summary="Berichte">
    <tr>
    	<th style="width: 100px">Erstellungsdatum</th>
        <th style="width: 500px">Name</th>
    </tr>
	<?php $_from = $this->_tpl_vars['module']['berichte']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_2d9a4b2333ea678cc93f413ce61afaa0):
        $this->_tpl_vars['bericht'] = $_2d9a4b2333ea678cc93f413ce61afaa0
?>
	
	<?php echo smarty_function_cssmodulo(array('class' => 'modulo','varname' => 'tmp'), $this);?>

	
	<tr>
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><?php echo $this->_tpl_vars['bericht']['datum']; ?>
</td>
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><a href="<?php echo $this->_tpl_vars['bericht']['links']['item']; ?>
"><?php echo $this->_tpl_vars['bericht']['title']; ?>
</a> </td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
</table>