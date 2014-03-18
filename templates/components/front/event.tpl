<div id="{$event.uid}" data-postType="{$event.post_type}" data-postId="{$event.event_id}" data-entityType="event" class="event_container {if $event.f_old}old_event{/if}">
    <div class="event_date">
        {if $event.start_ts}
            {if $event.f_only_month}
                {assign var='output_date_format' value=$only_month_date_format}
            {else}
                {assign var='output_date_format' value=$date_format}
            {/if}
            {$event.start_ts|date_format:$output_date_format}
        {else}
            &nbsp;
        {/if}
        {if $event.end_ts && !$event.f_only_month}
            -<br>{$event.end_ts|date_format:$date_format}
        {/if}
    </div>
    <div class="event_name">
        <div>
            <a href="{$event.url|default:''}" class="event_name_link">{$event.name}</a>
        </div>
        {if $is_admin}
            <div class="approving_div">
                <div class="appr_status {if !$event.f_approved}unapproved{/if}">{if $event.f_approved}Approved{else}Unapproved{/if}</div>
                <a href="" class="delete_event">Delete</a>
                <a href="" class="appr_status_edit">Edit</a>
            </div>
        {/if}

        <!--div class="event_tags">
        </div>
        <div class="event_tags">
        </div-->
    </div>
    <div class="event_location">
        {$event.location|escape|truncate:18:"...":true}
    </div>
    <div class="clear"></div>
</div>