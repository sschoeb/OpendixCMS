<?php /* Smarty version 2.6.18, created on 2011-04-03 18:52:07
         compiled from ../../templates/tcgrabs//css/slimbox2.css */ ?>
<?php echo '
/* SLIMBOX */

#lbOverlay {
	position: fixed;
	z-index: 9999;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	background-color: #311;
	cursor: pointer;
}

#lbCenter, #lbBottomContainer {
	position: absolute;
	z-index: 9999;
	overflow: hidden;
	background-color: #fff;
}

.lbLoading {
	background: #fff url(';  echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
loading.gif<?php echo ') no-repeat center;
}

#lbImage {
	position: absolute;
	left: 0;
	top: 0;
	border: 10px solid #fff;
	background-repeat: no-repeat;
}

#lbPrevLink, #lbNextLink {
	display: block;
	position: absolute;
	top: 0;
	width: 50%;
	outline: none;
}

#lbPrevLink {
	left: 0;
}

#lbPrevLink:hover {
	background: transparent url(';  echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
prevlabel.gif<?php echo ') no-repeat 0 15%;
}

#lbNextLink {
	right: 0;
}

#lbNextLink:hover {
	background: transparent url(';  echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
nextlabel.gif<?php echo ') no-repeat 100% 15%;
}

#lbBottom {
	font-family: Verdana, Arial, Geneva, Helvetica, sans-serif;
	font-size: 10px;
	color: #ff3300;
	line-height: 1.4em;
	text-align: left;
	border: 10px solid #fff;
	border-top-style: none;
}

#lbCloseLink {
	display: block;
	float: right;
	width: 66px;
	height: 22px;
	background: transparent url(';  echo $this->_tpl_vars['cms']['path']['template']['image']; ?>
closelabel.gif<?php echo ') no-repeat center;
	margin: 5px 0;
	outline: none;
}

#lbCaption, #lbNumber {
	margin-right: 71px;
}

#lbCaption {
	font-weight: bold;
}
'; ?>