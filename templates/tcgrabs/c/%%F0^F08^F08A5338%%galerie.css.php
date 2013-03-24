<?php /* Smarty version 2.6.18, created on 2011-04-09 16:40:57
         compiled from ../../templates/tcgrabs//css/galerie.css */ ?>

<?php echo '
/*
******************************************************
*
*    Galerie - Box
*
*
*/

.galeriebox {
        width: 160px; height: 130px;
        float: left;
        margin: 10px 18px 10px 0 ; padding: 5px 18px;       
        background: #f0f0f0 url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'menu-bg.gif\') 0px 0px repeat-x;
        border:1px solid #ddd;   
        border-radius:  3px;
}


.galeriebox a {
        display: block;
        border: 1px solid #aaa;
        border-top: 1px solid #888;        
        border-left: 1px solid #888;
        height: 100px; width: 150px;
        margin: 10px auto 10px auto;
        outline: 0px;
}

.galeriebox:hover {background: #eee; cursor: pointer; border:1px solid #ccc}











'; ?>