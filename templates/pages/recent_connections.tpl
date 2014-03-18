{include file='components/header_new.tpl' active=''}

<div class="page_sizer_wide rails_container">
    <div class="left_rail">
        <div>
            <a id="show_followers" class="post_type_link fleft">Followers</a>
            <a id="show_following" class="post_type_link fleft">Joined</a>
            <div class="clear"></div>
        </div>
        <div class="content_container">
            <div id="following_div" class="hide">
                {if $joined_people}
                    {foreach from=$joined_people item=user}
                        {include file='components/front/front_follows.tpl' follow=$user}
                    {/foreach}
                {else}
                    You have no users joined recently
                {/if}
                <div class="clear"></div>
            </div>
            <div id="followers_div" class="hide">
                {if $users_followed}
                    {foreach from=$users_followed item=user}
                        {include file='components/front/front_follows.tpl' follow=$user}
                    {/foreach}
                {else}
                    No one followed you recently
                {/if}
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div class="right_rail">
        <div class="right_rail_content">
            {include file='components/front/front_right_post_buttons.tpl'}
            {include file='components/front/invite_link.tpl' type='user'}
        </div>
    </div>
    <div class="clear"></div>
</div>

{include file='components/js_common.tpl'}

<script>
    var tab_selected = '{$tab_selected}';

    $(document).ready(function() {

        setRightRailFixed();
        prepareFollowing();
        prepareFollowIconsSwitch();
        prepareCopyToClipboard();
        prepareSharing();

        if (tab_selected === 'followers') {
            $("#show_followers").click();
        } else {
            $("#show_joined").click();
        }
    });
</script>
{include file='components/footer_new.tpl'}