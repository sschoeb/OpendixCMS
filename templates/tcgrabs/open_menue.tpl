
<ul class="menue">
{foreach item=group from=$cms.menue}
	{defun name="menurecursion" list=$group.items} 
		
		
		{foreach item=element from=$list}
		
			{if $element.visible && !($element.type == 4 && !$cms.user.isdeclared)}
				
				<li class="menuelevel{$element.level}"><a {if $element.selected}class="active" {/if} {if $element.type==3} target="_blank" {/if}  href="{$element.link}">{$element.name}</a></li>
					{if $element.children != ''}
						{if $element.level > 0}
							<li><ul class="menue">
						{/if}
					{fun name="menurecursion" list=$element.children}
					{if $element.level > 0}
						</ul></li>
					{/if}
				{/if}
			{/if}
		{/foreach}
		
		
		
	{/defun}
{/foreach}
</ul>


<!-- <ul>
	<li><a href="index.html" title="Startseite">Startseite</a></li>
	<li><a href="#" title="Club">Club</a></li>
	<li>
	<ul>
		<li><a href="#" title="Clubdaten">Clubdaten</a></li>
		<li><a href="#" title="Struktur">Struktur</a></li>
		<li><a class="active" href="#" title="Statuten &amp; Reglemente">Statuten
		&amp; Reglemente</a></li>
		<li><a href="personen.html" title="Mitgliedschaft">Mitgliedschaft</a></li>
		<li><a href="#" title="Kontakt">Kontakt</a></li>

		<li><a href="#" title="Standort &amp; Wetter">Standort &amp; Wetter</a></li>
	</ul>
	</li>
	<li><a href="anlaesse.html" title="Anlässe">Anlässe</a></li>
	<li><a href="#" title="Interclub">Interclub</a></li>
	<li><a href="galerien.html" title="Bilder-Galerien">Bilder-Galerien</a></li>
	<li><a href="#" title="Sportverein-t">Sportverein-t</a></li>
	<li><a href="#" title="Junioren &amp; Schüler">Junioren &amp; Schüler</a></li>
	<li>
	<ul>
		<li><a href="#" title="Juniorenteam">Juniorenteam</a></li>
		<li><a href="#" title="Junioreninterclub">Junioreninterclub</a></li>
	</ul>
	</li>
	<li><a href="#" title="Tennisschule Illich">Tennisschule Illich</a></li>
	<li><a href="gbook.html" title="Gästebuch">Gästebuch</a></li>
	<li><a href="#" title="Sponsoren">Sponsoren</a></li>
	<li><a href="#" title="Links">Links</a></li>
</ul> -->