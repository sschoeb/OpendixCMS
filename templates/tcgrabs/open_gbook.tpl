<h1>G&auml;stebuch vom Tennis Club Grabs</h1>
            <div id="neuer_eintrag">
            <h2>Einen neuen G&auml;stebucheintrag verfassen</h2>
                
                <form name="search" action="{$module.addLink}" method="post" enctype="text/html">
                    
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
            
            {foreach item=eintrag from=$module.gbook.daten}
            
            <div class="eintrag">
                <div class="left">
                <p><b>Name:</b><br />{$eintrag.name}</p>
                <p><b>Datum:</b><br />{$eintrag.date}</p>
                <p><b>E-Mail:</b><br />{mailto address=$eintrag.mail encode="javascript"}</p>
                </div>            
                <div class="right">
                {$eintrag.text}
                </div>
            <div class="clear"></div>
            </div>
 
            
            {/foreach}
            
            
                                   
            {include file=cms_browse.tpl info=$module.gbook.menue}
            </table>