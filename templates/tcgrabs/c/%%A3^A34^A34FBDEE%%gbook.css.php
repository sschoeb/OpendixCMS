<?php /* Smarty version 2.6.18, created on 2011-04-03 10:43:06
         compiled from ../../templates/tcgrabs//css/gbook.css */ ?>
<?php echo '

/*      gbook    */

#neuer_eintrag {
        position: relative;
        width: 590px; height: 290px;
        background: #f0f0f0 url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'menu-bg.gif\') 0px 0px repeat-x;
        border: 1px solid #ccc; -moz-border-radius: 3px;
        padding: 10px 30px;
        font: 12px \'Lucida Sans Unicode\', arial;
}

#neuer_eintrag table {width: 600px; border-collapse: collapse; }
#neuer_eintrag table td { line-height: 28px; }
#neuer_eintrag .submit {
        width: 203px;
        border: 1px solid #aab;
        background: #dde url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'menu-bg.gif\') 0px -22px repeat-x;
        padding: 3px; margin: 10px 0px;
        font: bold 13px helvetica;
        color: #229900; cursor: pointer; 
        }
#neuer_eintrag .input {width: 340px; }
#neuer_eintrag .textarea { width: 340px; height: 120px; overflow: auto; }



/*      einzelne Einträge      */


.eintrag {
        border: 1px solid #ccc;
        width: 650px; 
        background: #fff url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'content-bg.gif\') -20px 0 repeat-y;
        -moz-border-radius: 6px;
        margin: 0 0 30px 0;
}

.eintrag .left {
        width: 150px; 
        float: left;
        font: 10px/20px \'Lucida Sans Unicode\', arial; 
        padding: 20px; 
       
}
.eintrag .left p{
        
        font: 12px/15px \'Lucida Sans Unicode\', arial;
        margin: 0 0 10px 0;

}

.eintrag .left p b{
        color: #229900;
        font: bold 12px/15px \'Trebuchet MS\';
        margin: 0 0 10px 0;

}


.eintrag .right {
        width: 420px;
        float: right;
        font: 12px/16px \'Lucida Sans Unicode\', arial;   padding: 15px ;  
}

'; ?>