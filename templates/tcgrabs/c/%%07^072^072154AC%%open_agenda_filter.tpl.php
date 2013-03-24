<?php /* Smarty version 2.6.18, created on 2011-04-03 10:44:40
         compiled from open_agenda_filter.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_select_date', 'open_agenda_filter.tpl', 9, false),)), $this); ?>
<div id="anlassfilter">
<h2>Bitte verfeinern Sie Ihre Suche</h2>

<form action="" method="post">

<table>
	<tr>
		<td>Startdatum</td>
		<td><?php echo smarty_function_html_select_date(array('field_array' => "filter[begin]",'prefix' => "",'time' => $this->_tpl_vars['module']['filter']['begin'],'year_empty' => 'Jahr','month_empty' => 'Monat','day_empty' => 'Tag','display_months' => false,'display_years' => false), $this);?>


		<?php echo smarty_function_html_select_date(array('field_array' => "filter[begin]",'prefix' => "",'time' => $this->_tpl_vars['module']['filter']['begin'],'year_empty' => 'Jahr','month_empty' => 'Monat','day_empty' => 'Tag','start_year' => "-5",'end_year' => "+5",'display_days' => false), $this);?>
</td>
	</tr>

	<tr>
		<td>Enddatum</td>
		<td><?php echo smarty_function_html_select_date(array('field_array' => "filter[end]",'prefix' => "",'time' => $this->_tpl_vars['module']['filter']['end'],'year_empty' => 'Jahr','month_empty' => 'Monat','day_empty' => 'Tag','display_months' => false,'display_years' => false), $this);?>


		<?php echo smarty_function_html_select_date(array('field_array' => "filter[end]",'prefix' => "",'time' => $this->_tpl_vars['module']['filter']['end'],'year_empty' => 'Jahr','month_empty' => 'Monat','day_empty' => 'Tag','start_year' => "-5",'end_year' => "+5",'display_days' => false), $this);?>
</td>
	</tr>

	<tr>
		<td>Suchbegriff (Stichwort)&nbsp;&nbsp;&nbsp;</td>
		<td><input class="input" type="text" name="filter[search]"
			value="<?php echo $this->_tpl_vars['module']['filter']['search']; ?>
" size="20" maxlength="50" /></td>
	</tr>

	<tr>
		<td>Welche Gruppe?</td>
		
		<td><select class="select" name="filter[type]" size="1">
			<option>Bitte w&auml;hlen</option>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cms_select.tpl", 'smarty_include_vars' => array('selectOptions' => $this->_tpl_vars['module']['filter']['type'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</select></td>
	</tr>

	<tr>
		<td>Nur bevorstehende Events <input class="checkbox" type="checkbox"
			name="filter[upcoming]" value="" <?php echo $this->_tpl_vars['module']['filter']['upcoming']; ?>
 /></td>
		<td><input class="submit" type="submit" name="setfilter"
			value="Veranstaltung finden" /></td>
	</tr>
</table>

</form>
</div>