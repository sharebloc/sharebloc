{if !empty($params)}
    <div class="track_params_div">
        {foreach from=$params key=key item=param}
            <div class="track_param_div">
                {$key}
                <input class="join_input front_custom_invite_input track_param" type="text" value="{$param}" name="{$key}">
            </div>
        {/foreach}
        <div class="track_param_apply">
            <a href="#" data-type="{$metric_id}" class="tracks_link">Apply</a>
        </div>
    </div>
{/if}

{if $type=='getEmailOpenRates'}
    <div class="track_params_div">
        {foreach from=$email_types key=key item=email_type}
            <a href="#" data-type="{$metric_id}" data-subtype="{$key}" class="tracks_link">{$email_type.title}</a> &nbsp;&nbsp;&nbsp;
        {/foreach}
    </div>
{/if}

<table class="tracksTable">
    <tr>
        {if empty($no_rows_numbers)}
            <th>#</th>
        {/if}
        {if $data}
            {foreach from=$data.0 key=key item=value}
                <th>{$key}</th>
            {/foreach}
        {/if}
    </tr>
    {if $data}
        {foreach from=$data item=row name=row_loop}
            {assign var='row_num' value=$smarty.foreach.row_loop.index+1}
            <tr id='data_row_{$row_num}' data-rowID='{$row_num}'>
                {if empty($no_rows_numbers)}
                    <td>{$row_num}</td>
                {/if}
                {foreach from=$row key=key item=value }
                    <td>
                        {if $type=='getAllUsersForDisable' && $key=='disable'}
                            <a class='action_button disable_user_btn' href='#' data-userId="{$row['user_id']}">Disable</a>
                        {else}
                            {$value}
                        {/if}
                    </td>
                {/foreach}
            </tr>
        {/foreach}
    {/if}
</table>