<form method="POST" action="{$cms.link.save}">
<div id="contentbox">
<h2>Allgemein</h2>
<table>

	<tr>
		<td>Name</td>
		<td><input type="text" name="galerie[name]" value="{$module.galerie.name}"
			size="50" /></td>
	</tr>
	<tr>
		<td>Datum</td>
		<td>{html_select_date field_array=galerie[date] prefix="" time=$module.galerie.date
		display_months=false display_years=false} {html_select_date
		prefix = "" field_array=galerie[date] time=$module.galerie.date start_year="-10"
		end_year="+10" display_days=false}</td>
	</tr>
	<tr>
		<td valign="top">Bemerkung</td>
		<td><textarea name="galerie[description]" cols="50" rows="4">{$module.galerie.description}</textarea>
		</td>
	</tr>
	<tr>
		<td></td>
		<td><input type="checkbox" name="galerie[active]" {$module.galerie.active} />
		Aktiv</td>
	</tr>
</table>
</div>
<br />
<div id="contentbox">
<h2>Photograf</h2>
<table align="center" width="90%">

	<tr>
		<td width="25px"><input type="radio" name="galerie[isUser]" value="1" {$module.galerie.isUser.known} /></td>
		<td>Bekannter Benutzer</td>
	</tr>
	<tr>
		<td></td>
		<td>Name
		<select name="galerie[knownPhotograf]">
			{include file="cms_select.tpl" selectOptions=$module.galerie.user}
			
		</select>
		</td>
	</tr>
	<tr>
		<td><input type="radio" name="galerie[isUser]" value="0" {$module.galerie.isUser.unknown} />
		</td>
		<td>Unbekannter Benutzer</td>
	</tr>
	<tr>
		<td></td>
		<td>Name<input type="text" name="galerie[photograf]" value="{$module.galerie.photograf}"></td>
	</tr>
</table>
</div>
<br />
<div id="contentbox">
<h2>Hinzugef&uuml;gte Bilder</h2>
<table>
	{foreach item=img from=$module.data}
	<tr>
		<td>{$img.path.image}</td>
		<td><a href="{$img.path.delete}"><img
			src="{$cms.path.template.image}delete.png" /></a></td>
	</tr>
	{/foreach}
</table>
</div>
<br />
<div id="contentbox">
<h2>Bilder hinzuf&uuml;gen</h2>
{if $module.galerie.importImgCount == 0}
<div id="noimages">Es befinden sich keine Bilder im Import-Ordner. Kein
Import m&ouml;glich.</div>
{else}
<div id="imagesAvailable">Es befinden sich momentan <span id="imgCount">{$module.galerie.importImgCount}</span>
Bilder im Import-Ordner. Klicken Sie auf den folgenden Button um den
Import zu starten
<input type="button" id="impButton" onclick="javascript:start({$module.galerie.id}, {$module.galerie.importImgCount});" value="Import starten" />
</div>
<ul class="dottedlist" id="impList">

</ul>

{/if}</div>
<br />
<input type="submit" name="save" value="Speichern" /> <input
	type="submit" name="saveandclose" value="Speichern und Schliessen" /> <input
	type="submit" name="cancel" value="Abbrechen" /> <input type="reset"
	name="reset" value="Reset" /></form>