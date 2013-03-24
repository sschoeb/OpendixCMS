<?php /* Smarty version 2.6.18, created on 2011-04-09 14:22:01
         compiled from open_interclub_result.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cssmodulo', 'open_interclub_result.tpl', 17, false),)), $this); ?>
<h1>Resultate</h1>

<iframe src="http://comp.swisstennis.ch/ic/servlet/ClubResult?ClubName=1097&Lang=D" width="670" height="1200" frameBorder="0"></iframe>

<!-- 
<?php if ($_GET['action'] == 'item'): ?>
<h1>Resultate - <?php echo $_GET['team']; ?>
</h1>
	
<?php else: ?>
<h1>Resultate</h1>
<table id="basetabelle">
	<tr>
		<th>Mannschaft</th>
	</tr>
	<?php $_from = $this->_tpl_vars['module']['teams']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_f894427cc1c571f79da49605ef8b112f):
        $this->_tpl_vars['team'] = $_f894427cc1c571f79da49605ef8b112f
?>
	<tr>
		<?php echo smarty_function_cssmodulo(array('class' => 'modulo','varname' => 'tmp'), $this);?>

		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><a href="<?php echo $this->_tpl_vars['team']['link']; ?>
"><?php echo $this->_tpl_vars['team']['name']; ?>
</a></td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
</table>
<?php endif; ?> -->