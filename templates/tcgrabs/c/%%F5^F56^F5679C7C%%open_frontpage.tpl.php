<?php /* Smarty version 2.6.18, created on 2011-04-11 22:22:51
         compiled from open_frontpage.tpl */ ?>
<h1>Ein sportliches Willkommen beim Tennis Club Grabs</h1>



<!-- Aktuelle Nachrichten -->
<div class="container news">
<h2>Aktuelle Nachrichten</h2>
<?php $_from = $this->_tpl_vars['module']['front']['news']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_508c75c8507a2ae5223dfd2faeb98122):
        $this->_tpl_vars['news'] = $_508c75c8507a2ae5223dfd2faeb98122
?> <tt><?php echo $this->_tpl_vars['news']['datum']; ?>
</tt>
<p><?php echo $this->_tpl_vars['news']['name']; ?>
 <?php if ($this->_tpl_vars['news']['url'] != ""): ?> [ <a href="<?php echo $this->_tpl_vars['news']['url']; ?>
">mehr</a> ]
<?php endif; ?></p>
<?php endforeach; endif; unset($_from); ?></div>

<!-- Anlässe -->
<div class="container anlass">
<h2>N&auml;chste Anl&auml;sse</h2>
<?php $_from = $this->_tpl_vars['module']['front']['termine']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_1a36d8dd67f35040eac98803685d958b):
        $this->_tpl_vars['termin'] = $_1a36d8dd67f35040eac98803685d958b
?> <tt><?php echo $this->_tpl_vars['termin']['begin']; ?>
</tt>
<p><?php echo $this->_tpl_vars['termin']['name']; ?>
 [ <a href="<?php echo $this->_tpl_vars['termin']['link']; ?>
">mehr</a> ]</p>
<?php endforeach; endif; unset($_from); ?></div>


<div class="clear"></div>


<!-- Newsletter -->
<div class="container newsletter">
<h2>Newsletter Abonnieren</h2>
<form name="newsletter" action="<?php echo $this->_tpl_vars['module']['link']['newsletter']; ?>
" method="post">
	
	<select class="nlselect" name="action" size="1">
		<option value="1" selected="selected">In den Newsletter eintragen</option>
		<option value="2">Meine E-Mail aus dem Verteiler l&ouml;schen</option>
	</select> 
	<input class="nlinput" type="text" name="email" value="Ihre E-Mail Adresse" size="20" maxlength="50" /> <input
	class="nlsubmit" type="submit" name="submit" value="Absenden" />
	
</form>
</div>


<!-- Sponsor -->
<div class="container sponsor">
<h2>Official Sponsors</h2>
<?php if ($this->_tpl_vars['module']['front']['sponsor']['url'] != ''): ?> <a
	href="<?php echo $this->_tpl_vars['module']['front']['sponsor']['url']; ?>
" target="_blank"> <?php endif; ?> <img
	class="bild" src="<?php echo $this->_tpl_vars['module']['front']['sponsor']['bild']; ?>
" alt="Sponsor" /> <?php if ($this->_tpl_vars['module']['front']['sponsor']['url'] != ''): ?> </a> <?php endif; ?></div>


<div class="clear"></div>
