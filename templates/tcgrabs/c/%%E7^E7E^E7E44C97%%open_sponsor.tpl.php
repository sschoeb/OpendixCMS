<?php /* Smarty version 2.6.18, created on 2011-04-24 13:32:13
         compiled from open_sponsor.tpl */ ?>
<h1>Sponsoren</h1>
<?php $_from = $this->_tpl_vars['module']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_fcc3b61c61079aeee2da39720d6cc32a):
        $this->_tpl_vars['spongroup'] = $_fcc3b61c61079aeee2da39720d6cc32a
?>

<div id="contentbox">
<h2><?php echo $this->_tpl_vars['spongroup']['name']; ?>
</h2>
<table>
	<?php $_from = $this->_tpl_vars['spongroup']['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_2ea037683b12298454acce4f3fe9d139):
        $this->_tpl_vars['thesponsor'] = $_2ea037683b12298454acce4f3fe9d139
?>
	<tr>
		<td class="sponsorrow<?php if ($this->_tpl_vars['thesponsor']['last']): ?>last<?php endif; ?>" width="300px"><b><?php echo $this->_tpl_vars['thesponsor']['name']; ?>
</b>
		<?php if ($this->_tpl_vars['thesponsor']['url'] != ""): ?>(<a href="<?php echo $this->_tpl_vars['thesponsor']['url']; ?>
" target="_blank"
			title="<?php echo $this->_tpl_vars['thesponsor']['name']; ?>
">Webseite</a>)<?php endif; ?> <br /><?php echo $this->_tpl_vars['thesponsor']['beschreibung']; ?>
</td>
		<td class="sponsorrow<?php if ($this->_tpl_vars['thesponsor']['last']): ?>last<?php endif; ?>"><?php if ($this->_tpl_vars['thesponsor']['bild'] != ""): ?> <?php if ($this->_tpl_vars['thesponsor']['url'] != ""): ?> <a
			href="<?php echo $this->_tpl_vars['thesponsor']['url']; ?>
" target="_blank" title="<?php echo $this->_tpl_vars['thesponsor']['name']; ?>
"><img
			src="<?php echo $this->_tpl_vars['thesponsor']['bild']; ?>
" alt="<?php echo $this->_tpl_vars['thesponsor']['name']; ?>
" /></a> <?php else: ?> <img
			src="<?php echo $this->_tpl_vars['thesponsor']['bild']; ?>
" alt="<?php echo $this->_tpl_vars['thesponsor']['name']; ?>
"> <?php endif; ?> <?php else: ?> <?php endif; ?></td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
</table>
</div>
<br>

<?php endforeach; endif; unset($_from); ?>
