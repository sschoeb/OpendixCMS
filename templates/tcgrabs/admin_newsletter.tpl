<h1>Administration - Newsletter</h1>
<form method="post" action="{$cms.link.save}">
<textarea name="data" rows="30" cols="80">
{foreach item=email from=$module.emails}
{$email.email};
{/foreach}
</textarea>
<input type="submit" name="save" value="Speichern" />
</form>