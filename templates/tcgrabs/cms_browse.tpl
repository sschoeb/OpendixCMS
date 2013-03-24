
{if $info.site.total != ''}

<table id="pagination" summary="">
    <tr>
    	
        <td class="anfang{if $info.link.start != ""}"><a href="{$info.link.start}">Anfang</a>{else}dis">Anfang{/if}</td>
        <td class="zurueck{if $info.link.back != ''}"><a href="{$info.link.back}">zur&uuml;ck</a>{else}dis">zur&uuml;ck{/if}</td>
        <td>Seite {$info.site.current} von insgesamt {$info.site.total}</td>
        <td class="vor{if $info.link.next != ''}"><a href="{$info.link.next}">vorw&auml;rts</a>{else}dis">vorw&auml;rts{/if}</td>
        <td class="ende{if $info.link.end != ''}"><a href="{$info.link.end}">Ende</a>{else}dis">Ende{/if}</td>
    </tr>
</table>

{/if}