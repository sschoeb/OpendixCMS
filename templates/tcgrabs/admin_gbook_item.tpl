<h1>Administration - G&auml;stebuch - Element</h1>
<form action="{$cms.link.save}" method="post">
<div id="contentbox">
<h2>Allgemeine Infos</h2>
<table>
	<tr>
		<td style="width:100px">Erstellt von:</td>
		<td>{$module.daten.name} ({$module.daten.ip})</td>
	</tr>
	<tr>
		<td>Erstellt am:</td>
		<td>{$module.daten.date}</td>
	</tr>
</table>
</div>
<br />
<div id="contentbox">
<h2>Angaben</h2>
<table>
	<tr>
		<td style="width:100px">Name:</td>
		<td><input type="text" size="40" value="{$module.daten.name}" name="name"></td>
	</tr>
	<tr>
		<td >E-Mail:</td>
		<td><input type="text" size="40" value="{$module.daten.mail}" name="mail"></td>
	</tr>
	<!-- 
	<tr>
		<td>Homepage:</td>
		<td><input type="text" size="40" value="{$module.daten.homepage}" name="homepage">
		</td>
	</tr>
	 -->
</table>
</div>
<br />

<div id="contentbox">
<h2>Eintrag</h2>
<table>
	<tr>
		<td><textarea rows="10" cols="81" name="eintrag">{$module.daten.eintrag}</textarea>
		</td>
	</tr>
</table>
</div>
<br />
<input type="submit" name="save" value="Speichern" /> <input
	type="submit" name="saveandclose" value="Speichern und Schliessen" /> <input
	type="submit" name="cancel" value="Abbrechen" /> <input type="reset"
	name="reset" value="Reset" /></form>