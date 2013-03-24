<?php /* Smarty version 2.6.18, created on 2011-04-03 10:44:40
         compiled from open_agenda_overview.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cssmodulo', 'open_agenda_overview.tpl', 20, false),)), $this); ?>
<h1>Eine bestimmte Veranstaltung finden</h1>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "open_agenda_filter.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<br />
<br />
<h1>Termine und Veranstaltungen des Clubs</h1>
<table id="anlasstabelle" summary="Anlässe">
	<tr>
		<th style="width: 100px">Datum</th>
		<th style="width: 100px">Status</th>
		<th style="width: 400px">Veranstaltung</th>
		<th class="center" style="width: 20px">VCF</th>
	</tr>


	<?php $_from = $this->_tpl_vars['module']['daten']['daten']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_1a7d975851179bdd98063563ebe7f978):
        $this->_tpl_vars['anlass'] = $_1a7d975851179bdd98063563ebe7f978
?>

	<tr>
		<?php echo smarty_function_cssmodulo(array('class' => 'modulo','varname' => 'tmp'), $this);?>


		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><?php echo $this->_tpl_vars['anlass']['begin']; ?>
</td>
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><?php if ($this->_tpl_vars['anlass']['state'] == 1): ?> Durchgef&uuml;hrt <?php elseif ($this->_tpl_vars['anlass']['state'] == 2): ?>  Bevorstehend<?php else: ?> Abgesagt <?php endif; ?></td>
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><a href="<?php echo $this->_tpl_vars['anlass']['link']['item']; ?>
"><?php echo $this->_tpl_vars['anlass']['name']; ?>
</a></td>
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
 center"><a href="<?php echo $this->_tpl_vars['anlass']['link']['getvcs']; ?>
"><img
			src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/vcf.gif" width="16" height="11" style="border: 0px;" /></a></td>
	</tr>

	<?php endforeach; endif; unset($_from); ?>
</table>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_browse.tpl", 'smarty_include_vars' => array('info' => $this->_tpl_vars['module']['daten']['menue'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>