<?php /* Smarty version 2.6.18, created on 2011-04-09 09:12:57
         compiled from cms_timer.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_select_date', 'cms_timer.tpl', 7, false),array('function', 'html_select_time', 'cms_timer.tpl', 9, false),)), $this); ?>
<div id="contentbox">
<h2>Timer</h2>
<h3>Hinzuf&uuml;gen</h3>
<table>
	<tr>
		<td width="150px">Zeit:</td>
		<td><?php echo smarty_function_html_select_date(array('prefix' => "",'field_array' => 'addTimerDate','time' => $this->_tpl_vars['timerdata']['1']['date'],'display_months' => false,'display_years' => false), $this);?>

	<?php echo smarty_function_html_select_date(array('prefix' => "",'field_array' => 'addTimerDate','time' => $this->_tpl_vars['timerdata']['1']['date'],'end_year' => "+5",'display_days' => false), $this);?>

	<?php echo smarty_function_html_select_time(array('prefix' => "",'field_array' => 'addTimerDate','use_24_hours' => true,'display_seconds' => false,'time' => $this->_tpl_vars['timerdata']['1']['date']), $this);?>

		</td>
	</tr>
	<tr>
		<td>Action: </td>
		<td><select id="addTimerAction" name="timerAction"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_select.tpl", 'smarty_include_vars' => array('selectOptions' => $this->_tpl_vars['timerdata']['actions'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> </select></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="button" value="Timer hinzuf&uuml;gen" onclick="javascript:addTimer();"></td>
	</tr>
</table>

<br />
<h3 id="timertabletitle">Vorhandene Timer</h3>
<table id="timertable">
<?php $_from = $this->_tpl_vars['timerdata']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $_d41d8cd98f00b204e9800998ecf8427e => $_2c127bf32ccb4edf2bf22fea5a00e494):
        $this->_tpl_vars['timer'] = $_2c127bf32ccb4edf2bf22fea5a00e494
?>
<tr id="timeritem<?php echo $this->_tpl_vars['timer']['id']; ?>
"><td  width="150px"><?php echo $this->_tpl_vars['timer']['date']; ?>
</td><td><?php echo $this->_tpl_vars['timer']['name']; ?>
</td><td><a href="javascript:removeTimer(<?php echo $this->_tpl_vars['timer']['id']; ?>
);">L&ouml;schen</a></td></tr>
<?php endforeach; endif; unset($_from); ?>

</table>
</div>

<!-- <tr><td width="150px">22.04.2010 18:10</td><td>Aktivieren</td><td><a href="">Timer entfernen</a></td></tr>
<tr><td width="150px">22.04.2010 18:10</td><td>L&ouml;schen</td><td><a href="">Timer entfernen</a></td></tr>
 -->