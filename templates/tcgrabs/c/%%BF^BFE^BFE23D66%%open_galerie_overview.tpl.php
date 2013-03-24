<?php /* Smarty version 2.6.18, created on 2011-04-03 10:44:36
         compiled from open_galerie_overview.tpl */ ?>
<h1>Bildergalerien rund um den Tennis Club Grabs</h1>

<?php if (count ( $this->_tpl_vars['module']['galeries']['daten'] ) == 0): ?>
Es sind leider noch keine Galerien vorhanden.
<?php endif; ?>

<?php $_from = $this->_tpl_vars['module']['galeries']['daten']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_c564752a1e971d9e2d09d9cba750a13c):
        $this->_tpl_vars['galerie'] = $_c564752a1e971d9e2d09d9cba750a13c
?>
	<div class="galeriebox">
		<h2><?php echo $this->_tpl_vars['galerie']['name']; ?>
</h2>
		<a href="<?php echo $this->_tpl_vars['galerie']['link']; ?>
" style="background: #fff url('<?php echo $this->_tpl_vars['galerie']['thumb']; ?>
') 50% 50% no-repeat"></a>
		<p><?php echo $this->_tpl_vars['galerie']['imgCount']; ?>
 Bilder (Datum: <?php echo $this->_tpl_vars['galerie']['date']; ?>
)</p>
		<img class="lupe" src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/lupe.png" />
	</div>
<?php endforeach; endif; unset($_from); ?>

<div class="clear"></div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_browse.tpl", 'smarty_include_vars' => array('info' => $this->_tpl_vars['module']['galeries']['menue'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
