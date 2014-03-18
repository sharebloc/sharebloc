{include file='components/header_new.tpl'}

<div class="profile_header_container">
    <div class="page_sizer_wide user_header">
        <div class="profile_left_block">
            <img class="profile_image" alt="{$vendor.name|escape}" src="{$vendor.logo.my_url}" />
            <div class="profile_details_div">
                <div class="profile_full_name">{$vendor.name|escape}</div>
                <div class="profile_details_row">
                    {assign var=need_delimiter value=0}
                    {if $vendor.location}
                        <span class="profile_detail">{$vendor.location|escape}</span>
                        {assign var=need_delimiter value=1}
                    {/if}
                    {if $vendor.industry_tag}
                        {if $need_delimiter}
                            <span class="divider"> · </span>
                        {/if}
                        <a class="profile_detail" href="">{$vendor.industry_tag.tag_name}</a>
                        {assign var=need_delimiter value=1}
                    {/if}
                    {if $vendor.company_size!='unknown'}
                        {if $need_delimiter}
                            <span class="divider"> · </span>
                        {/if}
                        <span class="profile_detail">{$vendor.company_size} employee{if $vendor.company_size!='1'}s{/if}</span>
                    {/if}
                </div>
                <div class="profile_byline">{$vendor.about|escape|truncate:$max_about_length:""|nl2br}</div>
            </div>
        </div>
        <div class="profile_right_block">
            {if $is_admin}
                <div class="action_button profile_edit right_btn"><a href="{$vendor.my_url}/edit">Edit</a></div>
            {/if}
            <div class="action_button profile_follow {if $vendor.followed_by_curr_user}active{/if} right_btn"
                 data-whomType='vendor'
                 data-uid='{if $logged_in}{$user_info.uid}{/if}'
                 data-whomId='{$vendor.vendor_id}'>{if $vendor.followed_by_curr_user}Following{else}Follow{/if}
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
                <a class="post_type_link fright" id="my_feed_link" href="{$vendor.my_url}">Feed</a>
                <div class="clear"></div>
            </div>
            <div class="content_container">
                <div id="followers_div" class="more_container fleft hide"
                        data-offsetForMore="{$follows_on_page}"
                        data-noMore="{if (count($vendor.followers) < $follows_on_page)}1{else}0{/if}"
                        data-pageType="vendor"
                        data-entityID="{$vendor.vendor_id}"
                        data-followType="followers">

                    {foreach from=$vendor.followers item=follow}
                        {include file='components/front/front_follows.tpl'}
                    {/foreach}
                </div>
                <div class="clear"></div>
                <div class="front_loader_div hide"><img src="/images/loading.gif"></div>
            </div>
        </div>
    {else}
        <div class="left_rail">
            {include file='components/front/front_filters_block.tpl' filters_url=$vendor.my_url}

            <div class="content_container">
                {include file='components/front/front_type_filters_line.tpl' filters_url=$vendor.my_url}
                <div class="more_container"
                        data-offsetForMore="{$posts_on_page}"
                        data-noMore="{$no_more_content}"
                        data-pageType="vendor"
                        data-entityID="{$vendor.vendor_id}">
                {if $vendor_posts}
                    {foreach from=$vendor_posts item=front_post}
                        {include file='components/front/front_post.tpl' post=$front_post}
                    {/foreach}
                {else}
                    {$vendor.vendor_name|escape} has no posts
                {/if}
                </div>
                <div class="front_loader_div hide"><img src="/images/loading.gif"></div>
            </div>
        </div>
    {/if}
    <div class="right_rail">
        <div class="right_rail_content">
            {include file='components/front/front_right_post_buttons.tpl' vendor_id={$vendor.vendor_id}}
            <div class="profile_summary">
                <div class="summary_title">Summary</div>
                <div class="summary_text">
                    {$vendor.description|escape}
                </div>
                {if $vendor.website}
                    <div class="user_links">
                        <a href="{$vendor.website}" class="user_website trackedOutboundLink">{$vendor.website}</a>
                        <div class="clear"></div>
                    </div>
                {/if}
                <div class="user_links">
                    {if $vendor.facebook}<a href="{$vendor.facebook}" class="trackedOutboundLink"><img src="/images/icons/facebookicon.png"></a>{/if}
                    {if $vendor.twitter}<a href="{$vendor.twitter}" class="trackedOutboundLink"><img src="/images/icons/twittericon.png"></a>{/if}
                    {if $vendor.linkedin}<a href="{$vendor.linkedin}" class="trackedOutboundLink"><img src="/images/icons/linkedinicon.png"></a>{/if}
                    {if $vendor.google_plus}<a href="{$vendor.google_plus}" class="trackedOutboundLink"><img src="/images/icons/googleplusicon.png"></a>{/if}
                    <div class="clear"></div>
                </div>
            </div>
            {if $vendor.extended_tags}
                <div class="profile_summary bloc">
                    <div class="summary_title">Blocs</div>
                    <div class="bloc_tags">
                        {foreach from=$vendor.extended_tags item=tag}
                        <a href="{$tag.my_url}" class="bloc_tag {if !$tag.parent_tag_id}active{/if}">{$tag.tag_name}</a>
                        {/foreach}
                        <div class="clear"></div>
                    </div>
                </div>
            {/if}
            {if $show_content_type!='connections' && $vendor.followers}
                <div class="follows_btn_div">
                    <a class="post_type_link fleft active">Followers<span class="divider"> · </span><span data-followersCount='{$vendor.followers|count}' id='followers_count'>{$vendor.followers|count}</span></a>
                    <div class="clear"></div>
                </div>
                <div class="profile_summary followers">
                    {foreach from=$vendor.followers item=follow name=flw}
                        {if $smarty.foreach.flw.index < $max_follow_icons_number}
                            <a href="{$follow.my_url}"><img class="follow_logo_small" src="{$follow.logo.my_url}" data-followerUid='{$follow.entity_uid}'></a>
                        {/if}
                    {/foreach}
                    <div class="clear"></div>
                    <a class="small_link followers_see_all" href="{$vendor.my_url}/connections">See all</a>
                </div>
            {/if}
        </div>
    </div>
    <div class="clear"></div>
</div>

{include file='components/js_common.tpl'}
<script>
    $(document).ready(function() {
        setRightRailFixed();
        preparePageForMore();
        prepareContentFilters();
        prepareContentDelete();
        prepareVoting();
        prepareFollowing();
        prepareSharing();
        $("#show_followers").click();
    });
</script>

{include file='components/footer_new.tpl'}