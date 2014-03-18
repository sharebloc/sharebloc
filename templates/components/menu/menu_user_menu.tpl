<div id="user_menu_content" class="header_content">
    <div class="header_person" id="username_link">
        <img class="icon_header" src="{if isset($user_info.logo_hash)}/logos/{$user_info.logo_hash}_thumb.jpg{else}/images/nophoto.png{/if}">
        <a class="header_link" href="/users/{$user_info.code_name}/">{$user_info.first_name|escape}</a>
    </div>

    <div id="user_menu" class="hide">
        <a class="header_submenu" href="/users/{$user_info.code_name}/account">My Account</a><br>
        {if $is_admin}
            <a class="header_submenu" href="/track.php">Analytics</a><br>
            <a class="header_submenu" href="/invites_custom.php">Custom Invites</a><br>
            <a class="header_submenu" href="/companies/new/create">New Company</a><br>
            <a class="header_submenu" href="/send_emails.php?test=1">Test periodic emails</a><br>
            <a class="header_submenu" href="/autopost_from_rss.php?crawl_test=1">Test RSS crawler</a><br>
        {/if}
        <a class="header_submenu" href="/logout.php">Logout</a>
    </div>
</div>
{if $header_notifications.notifications}
    {include file='components/menu/menu_notifications.tpl'}
{/if}