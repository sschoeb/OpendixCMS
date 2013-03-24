<?php /* Smarty version 2.6.18, created on 2011-04-09 13:45:34
         compiled from open_interclub_player.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cssmodulo', 'open_interclub_player.tpl', 27, false),)), $this); ?>

<iframe src="http://www.swisstennis.ch/custom/includes/public/getLizenzSpieler.cfm?action=lizenz_spieler&mitnr=1097&geschlecht=0&sortOrder=1&abfrage=1&Suchart=1" width="700" height="2000" frameBorder="0">asd</iframe>


<!-- <?php if ($_GET['action'] == 'item'): ?>
<h1>Lizenzierte Spieler - <?php echo $_GET['name']; ?>
</h1>
<div class="interclub">
 -->
 asdasd

<!-- 
 <a href="<?php echo $this->_tpl_vars['module']['link']; ?>
" target="_blank">Resultate auf SwissTennis-Webseite</a>
<?php echo $this->_tpl_vars['module']['player']; ?>
 
</div>
<?php else: ?>
<h1>Lizenzierte Spieler</h1>

<table id="basetabelle">
	<tr>
		<th>Lizenz-Nr.</th>
		<th>Name</th>
		<th>Klassierung</th>
		<th>Punkte</th>
	</tr>
	<?php $_from = $this->_tpl_vars['module']['player']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_912af0dff974604f1321254ca8ff38b6):
        $this->_tpl_vars['player'] = $_912af0dff974604f1321254ca8ff38b6
?>
	<tr>
		<?php echo smarty_function_cssmodulo(array('class' => 'modulo','varname' => 'tmp'), $this);?>

		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><?php echo $this->_tpl_vars['player']['lizenznr']; ?>
</td>
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><a href="javascript:ShowPopup('<?php echo $this->_tpl_vars['player']['links']['item']; ?>
', 650, 600);" ><?php echo $this->_tpl_vars['player']['name']; ?>
</a></td>
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><?php echo $this->_tpl_vars['player']['klass']; ?>
</td>
		<td class="<?php echo $this->_tpl_vars['tmp']; ?>
"><?php echo $this->_tpl_vars['player']['klasswert']; ?>
</td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
</table>
<?php endif; ?>
 -->