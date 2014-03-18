{if $step>0}
<div class="simple_text_div">
    Already entered data:<br><br>

    {section name=net_loop start=0 loop=$step step=1}
        {assign var="step_network" value="{$networks[$smarty.section.net_loop.index]}"}
        {assign var="step_id" value="{$step_network.id}"}
        {$step_network.display_name} profile:
        {if $vendor.$step_id.network_link}
            <a class="popup_link" href="{$vendor.$step_id.network_link}">{$vendor.$step_id.network_link}</a>
        {else}
            none
        {/if}
        <br>
    {/section}
</div>
{/if}