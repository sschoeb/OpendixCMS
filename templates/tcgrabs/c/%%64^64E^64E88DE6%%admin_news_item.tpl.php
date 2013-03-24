<?php /* Smarty version 2.6.18, created on 2011-04-09 14:03:02
         compiled from admin_news_item.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_select_date', 'admin_news_item.tpl', 18, false),array('function', 'html_select_time', 'admin_news_item.tpl', 22, false),)), $this); ?>
<form method="POST" action="<?php echo $this->_tpl_vars['cms']['link']['save']; ?>
"
	enctype="multipart/form-data">
<h1>Administration - News - Element</h1>
<div id="contentbox">
<h2>Allgemein</h2>
<table>
	<tr>
		<td>Name:</td>
		<td><input type="text" name="news[name]" size="60" value="<?php echo $this->_tpl_vars['module']['item']['name']; ?>
" /></td>
	</tr>
	<!-- <tr>
		<td>Text:</td>
		<td><textarea name="news[text]"><?php echo $this->_tpl_vars['module']['item']['text']; ?>
</textarea></td>
	</tr> -->
	<tr>
		<td>Zeit:</td>
		<td>
		<?php echo smarty_function_html_select_date(array('prefix' => "",'field_array' => "news[time]",'time' => $this->_tpl_vars['module']['item']['time'],'display_months' => false,'display_years' => false), $this);?>

		<?php echo smarty_function_html_select_date(array('prefix' => "",'field_array' => "news[time]",'time' => $this->_tpl_vars['module']['item']['time'],'end_year' => "+5",'display_days' => false), $this);?>

		<?php echo smarty_function_html_select_time(array('prefix' => "",'field_array' => "news[time]",'time' => $this->_tpl_vars['module']['item']['time'],'use_24_hours' => true,'display_seconds' => false), $this);?>
</td>
	</tr>
	<tr>
		<td></td>
		<td><input type="checkbox" name="news[active]" <?php echo $this->_tpl_vars['module']['item']['active']; ?>
 />
		Aktiv</td>
	</tr>
</table>

<!-- Startseite: <input type="checkbox" name="news[frontpage]" <?php echo $this->_tpl_vars['module']['item']['frontpage']; ?>
 /><br />
Panel: <input type="checkbox" name="news[panel]" <?php echo $this->_tpl_vars['module']['item']['panel']; ?>
 /><br /> -->

<!-- newsfeed: <input type="checkbox" name="news[newsfeed]" <?php echo $this->_tpl_vars['module']['item']['newsfeed']; ?>
 /> -->
</div>

<br />
<div id="contentbox">
<h2>Verlinkung</h2>
<h3>Links hinzuf&uuml;gen</h3>
<input id="linkname" type="hidden" name="news[link][name]" value="newLink" />
<input type="radio" name="news[link][type]" value="FILE" onchange="javascript:radioBoxChange();" <?php if ($this->_tpl_vars['module']['item']['linktype'] == 'FILE'): ?>checked<?php endif; ?> />Filebase<br />
<div class="linkFile" id="linkFile">
 <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_fbbrowser.tpl", 'smarty_include_vars' => array('function' => 'takeNewsFile')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><br />

Ausgew&auml;hlte Datei: <br /><input type="text" id="filebaseFile" name="news[link][filebase]" value="<?php echo $this->_tpl_vars['module']['item']['filebase']; ?>
" size="50" readonly /><br />
</div> 

<input type="radio" name="news[link][type]" value="WEBSITE" onchange="javascript:radioBoxChange();" <?php if ($this->_tpl_vars['module']['item']['linktype'] == 'WEBSITE'): ?>checked<?php endif; ?>/>Webseite<br />
<div class="linkFile" id="linkWebsite">
Verlinkte Webseite:<br />
<input type="text" id="linkWebsiteInput" name="news[website]" size="50" value="<?php echo $this->_tpl_vars['module']['item']['website']; ?>
" /><br />
</div> <input type="radio" name="news[link][type]" value="INTERNAL"
	onchange="javascript:radioBoxChange();" <?php if ($this->_tpl_vars['module']['item']['linktype'] == 'INTERNAL'): ?>checked<?php endif; ?>/>Interne
Verlinkung<br />
<div class="linkFile" id="linkInternal"> <select id="connmenue" name="menueitem"
	onchange="javascript:connMenueChange();">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_menueselect.tpl", 'smarty_include_vars' => array('selectOptions' => $this->_tpl_vars['module']['item']['menueselect'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</select> <br />
<span id="sconnconn"> <select id="connconn"
	name="news[link][internal][connectyiontype]"
	onchange="javascript:connConnChange();" />
</select> <br />
</span> <span id="sconnelement"> <select id="connelement"
	name="news[link][internal][element]" />
</select> <br />
</span></div> <input type="button" onclick="javascript:AddNewLink();"
	value="Link hinzuf&uuml;gen"\> </span> <span id="linklistcontainer">
<h3>Vorhandene Links</h3>
<ul id="avaibleLinks">
	<?php $_from = $this->_tpl_vars['module']['item']['links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_2a304a1348456ccd2234cd71a81bd338):
        $this->_tpl_vars['link'] = $_2a304a1348456ccd2234cd71a81bd338
?>
	<li id="linkId<?php echo $this->_tpl_vars['link']['id']; ?>
"><a href="<?php echo $this->_tpl_vars['link']['link']['link']; ?>
"
		<?php if ($this->_tpl_vars['link']['linktype'] != 'FILEBASE'): ?> target="_blank"<?php endif; ?>><?php echo $this->_tpl_vars['link']['name']; ?>
</a>
	| <a href="javascript:DeleteLinkClick(<?php echo $this->_tpl_vars['link']['id']; ?>
);">L&ouml;schen</a></li>
	<?php endforeach; endif; unset($_from); ?>
</ul>
</span></div>
<br />


<br />
<input type="submit" name="save" value="Speichern" /> <input
	type="submit" name="saveandclose" value="Speichern und Schliessen" /> <input
	type="submit" name="cancel" value="Abbrechen" /> <input type="reset"
	name="reset" value="Reset" /></form>