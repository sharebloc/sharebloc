<div class="notifications_container header_content">
    <div class="notifications_circle_div">
        <div id="notifications_circle" class="notifications_circle">
            {$header_notifications.total_count_show}
        </div>
    </div>
    <div id="header_notifications_div" class="header_notifications_div hide">
        {foreach $header_notifications.notifications item=notification name=notify}
            <div class="header_notification">
                {foreach $notification.authors_html item=author name=count}
                    <a class="notification_author_link" href="{$author.my_url}" target="_blank">{$author.full_name|escape}</a>{if $smarty.foreach.count.index+1 < $notification.authors_html|count}, {/if}
                {/foreach}
                {if $notification.rest_authors_text}
                    and <a class="notification_author_link" href="{$notification.my_url}" target="_blank">{$notification.rest_authors_text}</a>
                {/if}
                commented on {if $notification.reason=='my_post_commented'}your{else}the{/if} post <a href="{$notification.my_url}">{$notification.post_title|escape|truncate:30}</a>
            </div>
        {/foreach}
        {if $header_notifications.show_see_all}
            <div class="header_notification">
                <a href="/users/{$user_info.code_name}/account?active_tab=notifications_tab&selected=notification">See All</a>
            </div>
        {/if}
    </div>
</div>