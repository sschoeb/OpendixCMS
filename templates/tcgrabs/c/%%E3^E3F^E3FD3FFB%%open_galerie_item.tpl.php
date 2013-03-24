<?php /* Smarty version 2.6.18, created on 2011-04-03 18:52:07
         compiled from open_galerie_item.tpl */ ?>
<h1><?php echo $this->_tpl_vars['module']['galerie']['name']; ?>
</h1>
<p><?php echo $this->_tpl_vars['module']['galerie']['description']; ?>
</p>

<?php $_from = $this->_tpl_vars['module']['images']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_b798abe6e1b1318ee36b0dcb3fb9e4d3):
        $this->_tpl_vars['img'] = $_b798abe6e1b1318ee36b0dcb3fb9e4d3
?>
<div class="galeriebox">
	<a href="<?php echo $this->_tpl_vars['img']['img']; ?>
" rel="lightbox[]" style="background: #fff url('<?php echo $this->_tpl_vars['img']['thumb']; ?>
') 50% 50% no-repeat"></a>
</div>
<?php endforeach; endif; unset($_from); ?>


