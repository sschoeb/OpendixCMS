<?php /* Smarty version 2.6.18, created on 2011-04-11 22:16:04
         compiled from open_menue.tpl */ ?>

<ul class="menue">
<?php $_from = $this->_tpl_vars['cms']['menue']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_db0f6f37ebeb6ea09489124345af2a45):
        $this->_tpl_vars['group'] = $_db0f6f37ebeb6ea09489124345af2a45
?>
	<?php if (!function_exists('smarty_fun_menurecursion')) { function smarty_fun_menurecursion(&$smarty, $params) { $_fun_tpl_vars = $smarty->_tpl_vars; $smarty->assign($params);  ?> 
		
		
		<?php $_from = $smarty->_tpl_vars['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_8e2dcfd7e7e24b1ca76c1193f645902b):
        $smarty->_tpl_vars['element'] = $_8e2dcfd7e7e24b1ca76c1193f645902b
?>
		
			<?php if ($smarty->_tpl_vars['element']['visible'] && ! ( $smarty->_tpl_vars['element']['type'] == 4 && ! $smarty->_tpl_vars['cms']['user']['isdeclared'] )): ?>
				
				<li class="menuelevel<?php echo $smarty->_tpl_vars['element']['level']; ?>
"><a <?php if ($smarty->_tpl_vars['element']['selected']): ?>class="active" <?php endif; ?> <?php if ($smarty->_tpl_vars['element']['type'] == 3): ?> target="_blank" <?php endif; ?>  href="<?php echo $smarty->_tpl_vars['element']['link']; ?>
"><?php echo $smarty->_tpl_vars['element']['name']; ?>
</a></li>
					<?php if ($smarty->_tpl_vars['element']['children'] != ''): ?>
						<?php if ($smarty->_tpl_vars['element']['level'] > 0): ?>
							<li><ul class="menue">
						<?php endif; ?>
					<?php smarty_fun_menurecursion($smarty, array('list'=>$smarty->_tpl_vars['element']['children']));  ?>
					<?php if ($smarty->_tpl_vars['element']['level'] > 0): ?>
						</ul></li>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>
		
		
		
	<?php  $smarty->_tpl_vars = $_fun_tpl_vars; }} smarty_fun_menurecursion($this, array('list'=>$this->_tpl_vars['group']['items']));  ?>
<?php endforeach; endif; unset($_from); ?>
</ul>


<!-- <ul>
	<li><a href="index.html" title="Startseite">Startseite</a></li>
	<li><a href="#" title="Club">Club</a></li>
	<li>
	<ul>
		<li><a href="#" title="Clubdaten">Clubdaten</a></li>
		<li><a href="#" title="Struktur">Struktur</a></li>
		<li><a class="active" href="#" title="Statuten &amp; Reglemente">Statuten
		&amp; Reglemente</a></li>
		<li><a href="personen.html" title="Mitgliedschaft">Mitgliedschaft</a></li>
		<li><a href="#" title="Kontakt">Kontakt</a></li>

		<li><a href="#" title="Standort &amp; Wetter">Standort &amp; Wetter</a></li>
	</ul>
	</li>
	<li><a href="anlaesse.html" title="Anlässe">Anlässe</a></li>
	<li><a href="#" title="Interclub">Interclub</a></li>
	<li><a href="galerien.html" title="Bilder-Galerien">Bilder-Galerien</a></li>
	<li><a href="#" title="Sportverein-t">Sportverein-t</a></li>
	<li><a href="#" title="Junioren &amp; Schüler">Junioren &amp; Schüler</a></li>
	<li>
	<ul>
		<li><a href="#" title="Juniorenteam">Juniorenteam</a></li>
		<li><a href="#" title="Junioreninterclub">Junioreninterclub</a></li>
	</ul>
	</li>
	<li><a href="#" title="Tennisschule Illich">Tennisschule Illich</a></li>
	<li><a href="gbook.html" title="Gästebuch">Gästebuch</a></li>
	<li><a href="#" title="Sponsoren">Sponsoren</a></li>
	<li><a href="#" title="Links">Links</a></li>
</ul> -->