<div id="contentbox">
<h2>Timer</h2>
<h3>Hinzuf&uuml;gen</h3>
<table>
	<tr>
		<td width="150px">Zeit:</td>
		<td>{html_select_date prefix="" field_array=addTimerDate time=$timerdata.1.date display_months=false display_years=false}
	{html_select_date prefix="" field_array=addTimerDate time=$timerdata.1.date end_year="+5"  display_days=false}
	{html_select_time prefix="" field_array=addTimerDate use_24_hours=true display_seconds=false time=$timerdata.1.date}
		</td>
	</tr>
	<tr>
		<td>Action: </td>
		<td><select id="addTimerAction" name="timerAction">{include file="cms_select.tpl" selectOptions=$timerdata.actions} </select></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="button" value="Timer hinzuf&uuml;gen" onclick="javascript:addTimer();"></td>
	</tr>
</table>

<br />
<h3 id="timertabletitle">Vorhandene Timer</h3>
<table id="timertable">
{foreach item=timer from=$timerdata.data}
<tr id="timeritem{$timer.id}"><td  width="150px">{$timer.date}</td><td>{$timer.name}</td><td><a href="javascript:removeTimer({$timer.id});">L&ouml;schen</a></td></tr>
{/foreach}

</table>
</div>

<!-- <tr><td width="150px">22.04.2010 18:10</td><td>Aktivieren</td><td><a href="">Timer entfernen</a></td></tr>
<tr><td width="150px">22.04.2010 18:10</td><td>L&ouml;schen</td><td><a href="">Timer entfernen</a></td></tr>
 -->