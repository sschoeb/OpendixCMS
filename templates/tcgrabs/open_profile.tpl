 {if $module.action == 'login'}
<h1>Login</h1>
<form method="post" action="{$module.loginlink}">
<div id="contentbox">
<table>
	<tr>
		<td style="width: 120px">Benutzername:</td>
		<td><input type="text" name="username" /></td>
	</tr>
	<tr>
		<td>Passwort:</td>
		<td><input type="password" name="password" /></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" value="Login" /></td>
	</tr>
</table>
</div>
</form>
{else}
<h1>Profil von {$module.user.nick}</h1>
<form method="post" action="{$cms.link.save}">
<div id="contentbox">
<h2>Kontakt</h2>
<table>
	<tr>
		<td  style="width: 200px">Vorname:</td>
		<td><input type="text" name="user[firstname]" value="{$module.user.firstname}" size=27 /></td>
	</tr>
	<tr>
		<td>Nachname:</td>
		<td><input type="text" name="user[name]" value="{$module.user.name}"  size=27/></td>
	</tr>
	<tr>
		<td>E-Mail:</td>
		<td><input type="text" name="user[email]" value="{$module.user.email}"  size=27/></td>
	</tr>
	<tr>
		<td>Telefon (Privat):</td>
		<td><input type="text" name="user[phoneprivate]" value="{$module.user.phoneprivate}" size=27 /></td>
	</tr>
	<tr>
		<td>Telefon (Gesch&auml;ft):</td>
		<td><input type="text" name="user[phonebusiness]" value="{$module.user.phonebusiness}" size=27 /></td>
	</tr>
	<tr>
		<td>Telfon (Mobile):</td>
		<td><input type="text" name="user[phonemobile]" value="{$module.user.phonemobile}" size=27 /></td>
	</tr>
	<tr>
		<td>Adresse:</td>
		<td><input type="text" name="user[street]" value="{$module.user.street}" size=27 /></td>
	</tr>
	<tr>
		<td>Plz/Ort:</td>
		<td><input type="text" name="user[zip]" value="{$module.user.zip}" size=4 />
		<input type="text" name="user[residence]" value="{$module.user.residence}" /></td>
	</tr>
</table>
</div>
<br />

<div id="contentbox">
<h2>System</h2>
<table>
	<tr>
		<td   style="width: 200px">Template:</td>
		<td><select name="user[template]">{include file="cms_select.tpl" selectOptions=$module.template}</select></td>
	</tr>
</table>
</div>
<br />
<div id="contentbox">
<h2>Passwort</h2>
<table>
	<tr>
		<td  style="width: 200px">Altes Passwort:</td>
		<td><input type="password" name="user[oldpassword]" /></td>
	</tr>
	<tr>
		<td>Neues Passwort:</td>
		<td><input type="password" name="user[newpassword]" /></td>
	</tr>
	<tr>
		<td>Neues Passwort(Best&auml;tigung):</td>
		<td><input type="password" name="user[newpasswordconf]" /></td>
	</tr>
</table>
</div>
<br />
<input type="submit" value="Speichern" />
</form>
{/if}
