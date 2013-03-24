<?php /* Smarty version 2.6.18, created on 2011-04-03 10:44:36
         compiled from ../../templates/tcgrabs//css/galerien.css */ ?>
<?php echo '

/*
******************************************************
*
*    Galerien - Boxen
*
*
*/

.galeriebox {
        position: relative;
        width: 160px; height: 170px;
        float: left;
        margin: 10px 18px 10px 0 ; padding: 5px 18px;       
        background: #f0f0f0 url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'menu-bg.gif\') 0px 0px repeat-x;
        border:1px solid #ddd;
        border-radius: 3px;
}

.galeriebox .lupe {
        position: absolute;
        top: 120px; right: 6px;
}


.galeriebox a {
        display: block;
        border: 1px solid #aaa;
        border-top: 1px solid #888;
        border-left: 1px solid #888;       
        height: 100px; width: 150px;
        margin: 6px auto 6px auto;
        outline: 0px;  -moz-border-radius: 3px;
}





.galeriebox h2 {
        width: 160px;
        color: #ff3500;
        margin: 5px 0 0 0;  
        font: normal 14px helvetica;
       

}

.galeriebox p {
        font: bold 9px arial;       
        color: #086FA1;
}



.galeriebox:hover {background: #eee; cursor: pointer; border:1px solid #ccc}




'; ?>