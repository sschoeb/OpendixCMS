<div id="anlassfilter">
<h2>Bitte verfeinern Sie Ihre Suche</h2>

<form action="" method="post">

<table>
	<tr>
		<td>Startdatum</td>
		<td>{html_select_date field_array=filter[begin] prefix=""
		time=$module.filter.begin year_empty='Jahr' month_empty='Monat'
		day_empty='Tag' display_months=false display_years=false}

		{html_select_date field_array=filter[begin] prefix=""
		time=$module.filter.begin year_empty='Jahr' month_empty='Monat'
		day_empty='Tag' start_year="-5" end_year="+5" display_days=false}</td>
	</tr>

	<tr>
		<td>Enddatum</td>
		<td>{html_select_date field_array=filter[end] prefix=""
		time=$module.filter.end year_empty='Jahr' month_empty='Monat'
		day_empty='Tag' display_months=false display_years=false}

		{html_select_date field_array=filter[end] prefix=""
		time=$module.filter.end year_empty='Jahr' month_empty='Monat'
		day_empty='Tag' start_year="-5" end_year="+5" display_days=false}</td>
	</tr>

	<tr>
		<td>Suchbegriff (Stichwort)&nbsp;&nbsp;&nbsp;</td>
		<td><input class="input" type="text" name="filter[search]"
			value="{$module.filter.search}" size="20" maxlength="50" /></td>
	</tr>

	<tr>
		<td>Welche Gruppe?</td>
		
		<td><select class="select" name="filter[type]" size="1">
			<option>Bitte w&auml;hlen</option>
			{include file=cms_select.tpl selectOptions=$module.filter.type}
		</select></td>
	</tr>

	<tr>
		<td>Nur bevorstehende Events <input class="checkbox" type="checkbox"
			name="filter[upcoming]" value="" {$module.filter.upcoming} /></td>
		<td><input class="submit" type="submit" name="setfilter"
			value="Veranstaltung finden" /></td>
	</tr>
</table>

</form>
</div>