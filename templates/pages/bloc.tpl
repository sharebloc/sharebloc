{include file='components/header_new.tpl'}

<div class="profile_header_container with_border_bottom">
    <div class="page_sizer_wide user_header">
        <div class="profile_left_block">
            {if $is_admin}
                <div id="change_logo" class="hide">
                    <div class="image_upload_div {if empty($tag.logo_hash)}no_logo{/if}" style="background-image:url('{$tag.logo.my_url_thumb}');">
                        <div class="image_upload_btn" id="image_upload" data-entityType="tag" data-entityID="{$tag.tag_id}"/></div>
                    </div>
                </div>
            {/if}
            <div class="profile_details_div">
                <div class="profile_full_name">{$tag.name|escape}
                    {if $show_rss}
                        <div class="rss_div"><a href="/rss{$tag.my_url}" target="_blank"><img src="/images/icons/rss_circle.png" title="RSS feed">&nbsp;</a></div>
                    {/if}
                </div>
                <div class="profile_details_row">
                    {if $tag.parent_tag_id}
                        <a class="profile_detail" href="{$tag.parent_tag_url}">{$tag.parent_tag_name|escape}</a>
                    {/if}
                </div>
{*   removed for now https://vendorstack.atlassian.net/browse/VEN-315              *}
{*                <div class="profile_byline">{$tag.description|escape|nl2br}</div>*}
            </div>
        </div>
        <div class="profile_right_block">
            {if $is_admin}
                <div id="change_logo_btn" class="action_button profile_edit right_btn">logo</div>
            {/if}
            <div class="action_button profile_follow {if $tag.followed_by_curr_user}active{/if} right_btn"
                 data-whomType='tag'
                 data-uid='{if $logged_in}{$user_info.uid}{/if}'
                 data-whomId='{$tag.tag_id}'>{if $tag.followed_by_curr_user}Following{else}Follow{/if}
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="page_sizer_wide rails_container">
    {if $show_content_type=='connections'}
        <div class="left_rail">
            <div>
                <a id="show_followers" class="post_type_link fleft active">Followers</a>
                <a class="post_type_link fright" id="my_feed_link" href="{$tag.my_url}">Feed</a>
                <div class="clear"></div>
            </div>
            <div class="content_container">
                <div id="followers_div" class="more_container fleft hide"
                <div id="followers_div" class="more_container fleft hide"
                        data-offsetForMore="{$follows_on_page}"
                        data-noMore="{if (count($tag.followers) < $follows_on_page)}1{else}0{/if}"
                        data-pageType="tag"
                        data-entityID="{$tag.tag_id}"
                        data-followType="followers">
                    {foreach from=$tag.followers item=follow}
                        {include file='components/front/front_follows.tpl'}
                    {/foreach}
                </div>
                <div class="clear"></div>
                <div class="front_loader_div hide"><img src="/images/loading.gif"></div>
            </div>
        </div>
    {else}
        {if $headline_posts}
            <div class="headline_posts_container"> {*to apply :last-child*}
                {foreach from=$headline_posts item=headline_post}
                    {include file='components/front/headline_post.tpl' post=$headline_post}
                {/foreach}
            </div>
            <div class="clear"></div>
            <div class="headline_post_spacer"></div>
        {/if}
        <div class="left_rail">
{* hiding for now https://vendorstack.atlassian.net/browse/VEN-326
            {if $tag.parent_tag_id}
                {* Not allowing to filter by tags for subtags *}
{*                {assign var=no_tags_filter value=true}
            {/if}
*}

            {include file='components/front/front_filters_block.tpl' filters_url=$tag.my_url}

            <div class="content_container">
                {include file='components/front/front_type_filters_line.tpl' filters_url=$tag.my_url}
                <div class="more_container"
                        data-offsetForMore="{$posts_on_page}"
                        data-noMore="{$no_more_content}"
                        data-pageType="tag"
                        data-entityID="{$tag.tag_id}">
                {if $tag_posts}
                    {foreach from=$tag_posts item=front_post}
                        {include file='components/front/front_post.tpl' post=$front_post}
                    {/foreach}
                {else}
                    {$tag.name|escape} has no posts
                {/if}
                </div>
                <div class="front_loader_div hide"><img src="/images/loading.gif"></div>
            </div>
        </div>
    {/if}
    <div class="right_rail">
        <div class="right_rail_content">
            {include file='components/front/front_right_post_buttons.tpl' tag_id=$tag.tag_id}
{*   removed for now https://vendorstack.atlassian.net/browse/VEN-315              *}
{*            {if ($tag.description)}
            <div class="profile_summary">
                <div class="summary_title">Summary</div>
                <div class="summary_text">
                    {$tag.description|escape}
                </div>
            </div>
            {/if}*}
            {if $show_contest_widget}
                {include file='contest_marketo/contest_widget_block.tpl'}
            {/if}
            {if $show_content_type!='connections' && $tag.followers}
                <div class="follows_btn_div">
                    <a class="post_type_link fleft active">Followers<span class="divider"> · </span><span data-followersCount='{$tag.followers_count}' id='followers_count'>{$tag.followers_count}</span></a>
                    <div class="clear"></div>
                </div>
                <div class="profile_summary followers">
                    {foreach from=$tag.followers item=follow name=flw}
                        {if $smarty.foreach.flw.index < $max_follow_icons_number}
                            <a href="{$follow.my_url}"><img class="follow_logo_small" src="{$follow.logo.my_url}" data-followerUid='{$follow.entity_uid}'></a>
                        {/if}
                    {/foreach}
                    <div class="clear"></div>
                    <a class="small_link followers_see_all" href="{$tag.my_url}/connections">See all</a>
                </div>
            {/if}
            {if $show_subscription_bloc}
                {include file='components/subscribe_block.tpl'}
            {/if}
        </div>
    </div>
    <div class="clear"></div>
</div>

{include file='components/js_common.tpl'}
<script>
    var tag_id = {$tag.tag_id};
    var disable_buttons = false;
    $(document).ready(function() {
        setRightRailFixed();
        preparePageForMore();
        prepareContentFilters();
        prepareContentDelete();
        prepareVoting();
        prepareFollowing();
        prepareImagesUpload();
        prepareSharing();
        $("#show_followers").click();
        $("#change_logo_btn").click(function(){
            $("#change_logo").show();
        });

        $("#subscription_popup_close").click(function(){
            $("#popup_subscription_container").hide();
            return false;
        });

        $("#subscribe_btn").click(function(){
            subscribe();
            return false;
        });
    });

    function subscribe(data) {
        if (disable_buttons) {
            return;
        }
        disable_buttons = true;
        var email = $("#subscribe_email").val();

        $.ajax({
            data: {
                cmd: 'subscribe',
                email:email,
                tag_id:tag_id
            },
            success: function(data) {
                if (data.status === 'success') {
                    $("#subscription_block").hide();
                    if (data.popup_message) {
                        $("#popup_subscription_container .standard_popup_content").text(data.popup_message);
                    }
                    $("#popup_subscription_container").show();
                } else {
                    alert(data.message);
                }
                disable_buttons = false;
            }
        });
    }
</script>

{include file='components/footer_new.tpl'}