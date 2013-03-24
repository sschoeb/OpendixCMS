<?php /* Smarty version 2.6.18, created on 2011-04-03 10:25:49
         compiled from cms_browse.tpl */ ?>

<?php if ($this->_tpl_vars['info']['site']['total'] != ''): ?>

<table id="pagination" summary="">
    <tr>
    	
        <td class="anfang<?php if ($this->_tpl_vars['info']['link']['start'] != ""): ?>"><a href="<?php echo $this->_tpl_vars['info']['link']['start']; ?>
">Anfang</a><?php else: ?>dis">Anfang<?php endif; ?></td>
        <td class="zurueck<?php if ($this->_tpl_vars['info']['link']['back'] != ''): ?>"><a href="<?php echo $this->_tpl_vars['info']['link']['back']; ?>
">zur&uuml;ck</a><?php else: ?>dis">zur&uuml;ck<?php endif; ?></td>
        <td>Seite <?php echo $this->_tpl_vars['info']['site']['current']; ?>
 von insgesamt <?php echo $this->_tpl_vars['info']['site']['total']; ?>
</td>
        <td class="vor<?php if ($this->_tpl_vars['info']['link']['next'] != ''): ?>"><a href="<?php echo $this->_tpl_vars['info']['link']['next']; ?>
">vorw&auml;rts</a><?php else: ?>dis">vorw&auml;rts<?php endif; ?></td>
        <td class="ende<?php if ($this->_tpl_vars['info']['link']['end'] != ''): ?>"><a href="<?php echo $this->_tpl_vars['info']['link']['end']; ?>
">Ende</a><?php else: ?>dis">Ende<?php endif; ?></td>
    </tr>
</table>

<?php endif; ?>