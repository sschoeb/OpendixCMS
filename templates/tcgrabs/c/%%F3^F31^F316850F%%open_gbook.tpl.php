<?php /* Smarty version 2.6.18, created on 2011-04-03 10:43:06
         compiled from open_gbook.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'mailto', 'open_gbook.tpl', 50, false),)), $this); ?>
<h1>G&auml;stebuch vom Tennis Club Grabs</h1>
            <div id="neuer_eintrag">
            <h2>Einen neuen G&auml;stebucheintrag verfassen</h2>
                
                <form name="search" action="<?php echo $this->_tpl_vars['module']['addLink']; ?>
" method="post" enctype="text/html">
                    
                    <table>
                     
                     <tr>
                        <td width="168">Ihr Name</td>
                        <td><input class="input" type="text" name="name" value="" size="20" maxlength="50" /></td>
                     </tr>
                     
                     <tr>
                        <td>E-Mail Adresse</td>
                        <td><input class="input" type="text" name="email" value="" size="20" maxlength="50" /></td>
                     </tr>
                    
                    <tr>
                        <td valign="top"></td>
                        <td><textarea onfocus="javascript:enterTextField();" class="textarea" id="nachricht" name="nachricht" rows="20" cols="20">Ihr Beitrag</textarea></td>
                    </tr>
                    
                    <tr>
                        <td>Spamschutz: 1 + 7 = ?</td>
                        <td><input onfocus="javascript:enterCaptchaField();" class="input" type="text" id="captcha" name="captcha" value="L&ouml;sung" size="20" maxlength="50" /></td>
                    </tr>
                    
                    <tr>
                    <td>&nbsp;</td>
                    <td>                    
                    <input class="submit" type="submit" name="#" value="Eintrag ver&ouml;ffentlichen" />
                    </td></tr>
                    
                    
                    </table>
                    
                </form>
              </div>
            
            
            <br /><br />
            
            <?php $_from = $this->_tpl_vars['module']['gbook']['daten']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_3d0c225b0fbe4965bba8ae58885cf060):
        $this->_tpl_vars['eintrag'] = $_3d0c225b0fbe4965bba8ae58885cf060
?>
            
            <div class="eintrag">
                <div class="left">
                <p><b>Name:</b><br /><?php echo $this->_tpl_vars['eintrag']['name']; ?>
</p>
                <p><b>Datum:</b><br /><?php echo $this->_tpl_vars['eintrag']['date']; ?>
</p>
                <p><b>E-Mail:</b><br /><?php echo smarty_function_mailto(array('address' => $this->_tpl_vars['eintrag']['mail'],'encode' => 'javascript'), $this);?>
</p>
                </div>            
                <div class="right">
                <?php echo $this->_tpl_vars['eintrag']['text']; ?>

                </div>
            <div class="clear"></div>
            </div>
 
            
            <?php endforeach; endif; unset($_from); ?>
            
            
                                   
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_browse.tpl", 'smarty_include_vars' => array('info' => $this->_tpl_vars['module']['gbook']['menue'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            </table>