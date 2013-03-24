<?php /* Smarty version 2.6.18, created on 2013-03-12 20:49:37
         compiled from ../../templates/tcgrabs//css/style.css */ ?>
<?php echo '
/*
*   orange:     #ff5500
*   gray:       #f0f0f0
*   darkgray    #4d4d4d
* 
* 
* */
html,body,div,p,h1,h2,h3,ul,ol,span,a,table,td,form,img,li {
	margin: 0;
	padding: 0;
	font-family: Verdana, Geneva, \'Lucida Sans Unicode\', arial, Helvetica,sans-serif;
}

.linkFile{
	background:#FFF;
	border: 1px solid #ccc;
	margin-top: 5px;
	margin-bottom: 10px;
	padding: 5px;
}

hr{
	color: #ccc;
	background-color: #ccc;
	height: 1px;
	border:none;
}

html {
	background: #F0F0F0 url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'html-bg.gif\') 0 0
		repeat-x;
	font-size: 101%;
}

body {
	font-size: 101%;
}

a {
	color: #229900;
	color: #086FA1;
}

a:hover {
	text-decoration: none;
}

h1 {
	color: #FF3500;
	margin: 10px 0px;
	font: normal 24px/ 24px helvetica;
	/*border-bottom: 1px solid #ccc; */
}

h2 {
	color: #009900;
	margin: 10px 0px;
	font: bold 16px/ 16px helvetica;
}

h3
{
	margin: 5px 0px;
	font: bold 12px/ 12px helvetica;
}


p {
	color: #223;
	
	font: 13px \'Lucida Sans Unicode\';
	text-align: justify;
}

ul {
	list-style-type: none;
}

ul.warning {
	color: #FF7F00;
}

div.warningbox {
	border: 1px solid #ccc;
	-moz-border-radius: 3px;
	padding: 10px 30px;
}

ul.confirmation {
	color: #229900;
}

div.confirmationbox {
	border: 1px solid #ccc;
	-moz-border-radius: 3px;
	padding: 10px 30px;
}

ul.dottedlist
{
	list-style: disc;
	margin: 25px;
		color: #223;
	
	font: inherit 13px \'Lucida Sans Unicode\';
	text-align: justify;
}

ul.numericlist
{
	list-style: decimal;
	margin-left: 40px;
	margin-top: 20px;
			color: #223;
	
	font: inherit 13px \'Lucida Sans Unicode\';
	text-align: justify;
}


ul.numericlist li
{
	margin-top: 15px;
}


ul.menue a {
	color: #333;
	text-decoration: none;
	font: bold 11px/ 30px arial;
	letter-spacing: 1px;
	display: block;
	padding: 0 15px;
	background: #f0f0f0;
	outline: 0px;
}

li.menuelevel0 a {
	width: 180px;
	height: 30px;
	border-bottom: 1px solid #ccc;
	border-top: 1px solid #fff;
}

li.menuelevel0 a:hover {
	background: #fff url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'menu-bg.gif\') 0px 0 repeat-x;
	color: #FF3300;
}

li.menuelevel0 a.active{
	color: #ff3500;
}

li.menuelevel1 a {
	height: 24px;
	width: 170px;
	font: bold 10px/ 24px arial;
	
	background: #fff url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'bullet.gif\') 30px 50% no-repeat;
	border-bottom: 1px solid #f0f0f0;
	padding: 0px 0px 0px 40px;
}

li.menuelevel1 a:hover {
	background: #f9f9f9 url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'activebullet.gif\') 30px 50% no-repeat;
	color: #FF3300;
}

li.menuelevel1 a.active{
	color: #ff3500;
}

li.menuelevel2 a {
	height: 24px;
	width: 150px;
	font: bold 10px/ 24px arial;
	
	background: #fff url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'bullet.gif\') 50px 50% no-repeat;
	border-bottom: 1px solid #f0f0f0;
	padding: 0px 0px 0px 60px;
}

li.menuelevel2 a.active{
	color: #ff3500;
}

li.menuelevel2 a:hover {
	background: #f9f9f9 url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'activebullet.gif\') 50px 50% no-repeat;
	color: #FF3300;
}

/*

      2. Ebene Menü   
ul.menue ul li a {
	height: 24px;
	width: 170px;
	font: bold 10px/ 24px arial;
	
	background: #fff url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'bullet.gif\') 30px 50% no-repeat;
	border-bottom: 1px solid #f0f0f0;
}

ul.menue ul li a:hover {
	background: #f9f9f9 url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'activebullet.gif\') 30px 50% no-repeat;
}

ul.menue li .active {
	color: #ff3500;
	font: bold 11px/ 30px arial;
}

ul.menue ul li .active {
	color: #ff3500;
	font: bold 10px/ 24px arial;
	background: #fff url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'activebullet.gif\')30px 50% no-repeat;
}

li.menuelevel0 a{
	
	padding: 0px 0px 0px 20px;
	
}

li.menuelevel1 a{
	font: bold 10px/ 24px arial;
	padding: 0px 0px 0px 40px;
	background: #fff url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'bullet.gif\') 30px 50% no-repeat;
	
	width: 170px;
	height: 30px;
	border-bottom: 1px solid #ccc;
	border-top: 1px solid #fff;
	
}

li.menuelevel2 a{
	width: 100px;
	font: bold 10px/ 24px arial;
	padding: 0px 0px 0px 60px;
	background: #fff url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'bullet.gif\') 30px 50% no-repeat;
}

ul.menue ul li ul li a:hover {
	background: #f9f9f9 url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'activebullet.gif\') 30px 50% no-repeat;
}

ul.menue li ul li .active {
	color: #ff3500;
	font: bold 11px/ 30px arial;
}

ul.menue ul li ul li .active {
	color: #ff3500;
	font: bold 10px/ 24px arial;
	background: #fff url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'activebullet.gif\')30px 50% no-repeat;
}


*/.clear {
	clear: both;
}

img { border: none; }

img.center
 {
 	display: block;
 	margin-left: auto;
 	margin-right: auto;
}

img.imgright {
	float: right;
	margin-left: 12px;
}

img.imgleft {
	float: left;
	margin-right: 12px;
}

img.imgcenter {
	margin: 10px auto;
}

#wrapper {
	width: 980px;
	margin: 0px auto 50px auto;
	padding: 20px 0 0 40px;
	background: url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'body-bg.jpg\') center 0px
		no-repeat;
}

#header {
	position: relative;
	width: 942px;
	height: 200px;
	background: transparent
		url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'header-bg.png\') center 50% no-repeat
		;
}

#contentbody {
	width: 940px;
	min-height: 500px;
	background: #fff url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'content-bg.gif\') 1px
		0 repeat-y;
	border: 1px solid #ccc;
}

#menue {
	width: 210px;
	float: left;
}

#content {
	width: 650px;
	float: right;
	min-height: 500px;
	padding: 20px 40px 20px 30px;
}

/*      Seitenavigation     */
#pagination {
	width: 500px;
	font: 12px \'Lucida Sans Unicode\', arial;
	text-align: center;
	margin: 15px auto;
	color: #777;
}

#pagination a {
	color: #086FA1;
	outline: 0px;
	text-decoration: none;
}

#pagination .anfang {
	padding: 0 0 0 10px;
	background: #fff url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'anfang.gif\') 0px 50%
		no-repeat;
}

#pagination .anfangdis {
	padding: 0 0 0 10px;
	background: #fff url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'anfangdis.gif\') 0px
		50% no-repeat;
}

#pagination .zurueck {
	padding: 0 0 0 8px;
	background: #fff url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'zurueck.gif\') 0px 50%
		no-repeat;
}

#pagination .zurueckdis {
	padding: 0 0 0 8px;
	background: #fff url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'zurueckdis.gif\') 0px
		50% no-repeat;
}

#pagination .vor {
	padding: 0 8px 0 0;
	background: #fff url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'vor.gif\') right 50%
		no-repeat;
}

#pagination .vordis {
	padding: 0 8px 0 0;
	background: #fff url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'vordis.gif\') right
		50% no-repeat;
}

#pagination .ende {
	padding: 0 12px 0 0;
	background: #fff url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'ende.gif\') right 50%
		no-repeat;
}

#pagination .endedis {
	padding: 0 12px 0 0;
	background: #fff url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'endedis.gif\') right
		50% no-repeat;
}

#impressum {
	float: right;
	margin: 5px 0px;
	color: #888;
	font: 10px \'Lucida Sans Unicode\', arial;
}

#impressum a {
	text-decoration: none;
	color: #778;
	font: 10px \'Lucida Sans Unicode\', arial;
}

.bild {
	border: 1px solid #ccc;
	-moz-border-radius: 3px;
	padding: 6px;
	background: #f0f0f0 url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'menu-bg.gif\') 0px
		0px repeat-x;
	margin: 5px 0 0 0;
}

.reglab{
	
	width: 140px;
    display: block;
    float: left;
    margin-top: 2px;
}

.abolab{
	
	width: 10em;
    display: block;
    float: left;
    margin-top: 2px;
}

.abotabelle tr input
{
	width: 100px;
	margin-top: 2px;
	margin-bottom: 2px;
}

.agendaadmintabelle tr input
{
	margin-top: 2px;
	margin-bottom: 2px;
}

.reginput{
	width:180px;
	margin-top: 2px;
}

.reginputzip{
	width:40px;
	margin-top: 2px;
	margin-right: 2px;
}

.reginputplace{
	width:132px;
	margin-top: 2px;
}

.reginputsel{
	margin-top: 2px;
	margin-bottom: 2px;
}

.sponsorrow{
	border-bottom: 1px solid #ccc;
	padding-bottom: 3px;
	padding-top: 3px;
	vertical-align: top;
	font: 12px \'Lucida Sans Unicode\', arial;
}

.sponsorrowlast{
	padding-bottom: 3px;
	padding-top: 3px;
	vertical-align: top;
	font: 12px \'Lucida Sans Unicode\', arial;
}

#basetabelle  { font: 12px \'Lucida Sans Unicode\', arial; width: 650px; border-collapse: collapse; border: 1px solid #ccc; }
#basetabelle th { font: bold 14px \'Lucida Sans Unicode\', arial; color: #009900; padding: 0px 10px; background: #f0f0f0 url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'menu-bg.gif\') 0px bottom repeat-x; text-align: left; line-height: 30px;}
#basetabelle td { padding: 0px 10px;  line-height:  28px; }
#basetabelle .modulo {background: #ffeeee;}
#basetabelle .center {text-align:center;}
#basetabelle a {color: #086FA1; outline: 0px;}

#sportvertabelle  {  font: 12px \'Lucida Sans Unicode\', arial; width: 650px; border-collapse: collapse; border: 1px solid #ccc; }
#sportvertabelle .header { border-right:1px solid #ccc; border-bottom:1px solid #ccc;  text-align:center; font: bold 14px \'Lucida Sans Unicode\', arial; color: #009900; padding: 0px 10px; background: #f0f0f0 url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'menu-bg.gif\') 0px bottom repeat-x;  line-height: 30px;}
#sportvertabelle td { vertical-align: top; padding-top:5px; padding-left: 10px; padding-right:10px; padding-bottom: 5px;  line-height:  18px; border-right:  1px solid #ccc; }

#sportvertabelle ul{
	list-style-type: disc;
	margin-left: 20px;
}

#sportvertabelle a {color: #086FA1; outline: 0px;}
#sportvertabelle .emptyfield {
		padding: 0px 10px;  
	line-height:  28px;
	border-right: 1px solid #ccc;
	border-bottom: 1px solid #ccc;
	border-top: 1px solid #fff;
	border-left: 1px solid #fff;
	background: none;
	color: #FF3500;
	font: bold 14px/ 14px helvetica;
}


#notfalltabelle  { font: 12px \'Lucida Sans Unicode\', arial; width: 650px; border-collapse: collapse; border: 1px solid #ccc; }
#notfalltabelle th { font: bold 14px \'Lucida Sans Unicode\', arial; color: #009900; padding: 0px 10px; background: #f0f0f0 url(\'';  echo $this->_tpl_vars['imgPath'];  echo 'menu-bg.gif\') 0px bottom repeat-x; text-align: left; line-height: 30px;}
#notfalltabelle td { padding: 5px 10px;  line-height:  18px; border: 1px solid #ccc; vertical-align: top;  }
#notfalltabelle .modulo {background: #ffeeee;}
#notfalltabelle .center {text-align:center;}
#notfalltabelle a {color: #086FA1; outline: 0px;}



'; ?>