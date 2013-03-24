<?php /* Smarty version 2.6.18, created on 2012-10-09 18:10:13
         compiled from open_index.tpl */ ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">

<head>
    <title>Tennis Club Grabs</title>
    <meta http-equiv="content-type" content="text/html;" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <!-- <link href="<?php echo $this->_tpl_vars['cms']['template']['path']['css']; ?>
/style.css" type="text/css" rel="stylesheet" /> -->
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_import.tpl", 'smarty_include_vars' => array('imgPath' => $this->_tpl_vars['cms']['path']['template']['image'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</head>



<body onload="run(new Array(<?php $_from = $this->_tpl_vars['cms']['runatstart']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_c70903749ed556d98a4966fdfb9ccd04):
        $this->_tpl_vars['jas'] = $_c70903749ed556d98a4966fdfb9ccd04
?>'<?php echo $this->_tpl_vars['jas']; ?>
',<?php endforeach; endif; unset($_from); ?>'empty'))">

    <div id="wrapper">
        <div id="header"></div> <!-- header end -->
        <div id="contentbody">
            <div id="menue">
               
               <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "open_menue.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
               
            	<a href="http://www.sport-verein-t.ch/" target="_blank"><img class="bild" style="margin: 10px 0 10px 8px;" alt="Sport-Vereint Logo" src="<?php echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
/sport-verein-t.jpg" /></a>           
            </div>
            <!-- menue end -->
            
            
            <div id="content">

			    
            	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_content.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            
            </div>
            <!-- content end -->
            
            <div class="clear"><br /></div>
            
            <div id="impressum">
                <a href="mailto:webmaster@tc-grabs.ch">Webmaster</a> |<a href="impressum.html">Impressum</a> | <a href="disclaimer.html">Disclaimer</a> | <a href="<?php echo $this->_tpl_vars['cms']['link']['login']; ?>
"><?php if ($this->_tpl_vars['cms']['user']['isdeclared'] == 1): ?>Logout<?php else: ?>Login<?php endif; ?></a> <!--|   <a href="http://www.macu-webdesign.de" target="_blank" title="Webdesign Uelzen">Webdesign</a>-->
            </div>
            
            </div> 
            <!-- contentbody end -->

    </div> <!-- wrapper end -->
<div class="clear">&nbsp;</div>
</body>
</html>