<?php /* Smarty version 2.6.18, created on 2011-04-03 10:43:31
         compiled from admin_content_overview.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cssmodulo', 'admin_content_overview.tpl', 20, false),)), $this); ?>
<form method="POST" action="<?php echo $this->_tpl_vars['cms']['link']['add']; ?>
">
<div id="contentbox">
<h2>Inhaltsseite hinzuf&uuml;gen</h2>

<input type="text" name="name" /> <input type="submit"
	value="Hinzuf&uuml;gen" name="AddBtn" /></div>
</form>
<br />


<table id="basetabelle">
	<tr>
		<th style="width: 99%">Name</th>
		<!-- <th>Aktiv</th> -->
		<th></th>

	</tr>
	<?php $_from = $this->_tpl_vars['module']['contents']['daten']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_9a0364b9e99bb480dd25e1f0284c8555):
        $this->_tpl_vars['content'] = $_9a0364b9e99bb480dd25e1f0284c8555
?>
	<tr>
		<?php echo smarty_function_cssmodulo(array('class' => 'modulo','varname' => 'tmp'), $this);?>

		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><a href="<?php echo $this->_tpl_vars['content']['link']['item']; ?>
"><?php echo $this->_tpl_vars['content']['name']; ?>
</a></td>
		<!-- <td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><a href="<?php echo $this->_tpl_vars['content']['link']['switchactive']; ?>
">Switchactive</a></td> -->
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><a href="<?php echo $this->_tpl_vars['content']['link']['delete']; ?>
"><img
			src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/delete.png" alt="L&ouml;schen" /></img></a></td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
</table>
