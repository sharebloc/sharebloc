{include file='components/header_new.tpl'}

<div class="profile_header_container">
    <div class="page_sizer_wide user_header">
        <div class="profile_left_block">
            <img class="profile_image" alt="{$user.full_name|escape}" src="{$user.logo.my_url}" />
            <div class="profile_details_div">
                <div class="profile_full_name">{$user.full_name|escape}</div>
                <div class="profile_details_row">
                    {assign var=need_delimiter value=0}
                    {if !empty($user.position)}
                        <span class="profile_detail">{$user.position|escape}</span>
                        {assign var=need_delimiter value=1}
                    {/if}
                    {if !empty($user.company.name)}
                        {if $need_delimiter}
                            <span class="divider"> · </span>
                        {/if}
                        <a class="profile_detail" href="{$user.company.my_url}">{$user.company.name|escape}</a>
                        {assign var=need_delimiter value=1}
                    {/if}
                    {if $user.location}
                        {if $need_delimiter}
                            <span class="divider"> · </span>
                        {/if}
                        <span class="profile_detail">{$user.location|escape}</span>
                        {assign var=need_delimiter value=1}
                    {/if}
                    {if $user.company_tag}
                        {if $need_delimiter}
                            <span class="divider"> · </span>
                        {/if}
                        <a class="profile_detail" href="{$user.company_tag.my_url}">{$user.company_tag.tag_name}</a>
                    {/if}
                </div>
                <div class="profile_byline">{$user.about|escape|truncate:$max_about_length:""|nl2br}</div>
            </div>
        </div>
        <div class="profile_right_block">
            {if $is_admin && $user.status!='admin'}
                <div class="action_button profile_suspend right_btn {if $user.status=='inactive'}suspended{/if}" data-isSuspended="{if $user.status=='active'}0{else}1{/if}">
                    {if $user.status=='active'}Suspend{else}Activate{/if}
                </div>
            {/if}
            {if $can_edit}
                <div class="action_button profile_edit right_btn"><a href="{$user.my_url}/profile">Edit</a></div>
            {/if}
            {if !$my_account}
                <div class="action_button profile_follow {if $user.followed_by_curr_user}active{/if} right_btn"
                     data-whomType='user'
                     data-uid='{if $logged_in}{$user_info.uid}{/if}'
                     data-whomId='{$user.user_id}'>{if $user.followed_by_curr_user}Following{else}Follow{/if}</div>
            {/if}
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="page_sizer_wide rails_container">
    {if $show_content_type=='connections'}
        <div class="left_rail">
            <div>
                <a id="show_following" class="post_type_link fleft">Following</a>
                <a id="show_followers" class="post_type_link fleft">Followers</a>
                {if $my_account}
                    <a id="show_recent_connections" class="post_type_link fleft">New Connections</a>
                {/if}
                <a class="post_type_link fright" id="my_feed_link" href="{$user.my_url}">Feed</a>
                <div class="clear"></div>
            </div>
            <div class="content_container">
                <div class="posts_filters_div">
                    Show Me:
                    <a class="posts_filter show_follow_type filter_active" data-showFollowType='all'>All</a>
                    <a class="posts_filter show_follow_type" data-showFollowType='user'>People</a>
                    <a class="posts_filter show_follow_type" data-showFollowType='vendor'>Companies</a>
                    <a class="posts_filter show_follow_type" data-showFollowType='tag'>Bloc</a>
                </div>
                <div id="following_div" class="more_container fleft hide"
                        data-offsetForMore="{$follows_on_page}"
                        data-noMore="{if (count($user.following) < $follows_on_page)}1{else}0{/if}"
                        data-pageType="user"
                        data-entityID="{$user.user_id}"
                        data-followType="following">
                    {if $user.following}
                        {foreach from=$user.following item=follow}
                            {include file='components/front/front_follows.tpl'}
                        {/foreach}
                    {else}
                        {if $my_account}You don't{else}{$user.full_name|escape} doesn't{/if} follow anyone
                    {/if}
                </div>
                <div class="clear"></div>
                <div id="followers_div" class="more_container fleft hide"
                        data-offsetForMore="{$follows_on_page}"
                        data-noMore="{if ($user.followers|count < $follows_on_page)}1{else}0{/if}"
                        data-pageType="user"
                        data-entityID="{$user.user_id}"
                        data-followType="followers">
                    {if $user.followers}
                        <div class="follow_all_div">
                            <input class="follow_all_chk" type="checkbox" data-whomType='user' checked>Follow/Unfollow All
                        </div>
                        {foreach from=$user.followers item=follow}
                            {include file='components/front/front_follows.tpl'}
                        {/foreach}
                    {else}
                        {if $my_account}You have{else}{$user.full_name|escape} has{/if} no followers
                    {/if}
                </div>
                <div class="clear"></div>
                {if $my_account}
                    <div id="recent_connections_div" class="hide">
                        {if $user.recent_connections}
                            <div class="follow_all_div">
                                <input class="follow_all_chk" type="checkbox" data-whomType='user' checked>Follow/Unfollow All
                            </div>
                            {foreach from=$user.recent_connections item=follow}
                                {include file='components/front/front_follows.tpl'}
                            {/foreach}
                        {else}
                            {if $my_account}You have{else}{$user.full_name|escape} has{/if} no recent connections
                        {/if}
                        <div class="clear"></div>
                    </div>
                {/if}
                <div class="front_loader_div hide"><img src="/images/loading.gif"></div>
            </div>
        </div>
    {else}
        <div class="left_rail">
            {include file='components/front/front_filters_block.tpl' filters_url=$user.my_url}

            <div class="content_container">
                {include file='components/front/front_type_filters_line.tpl' filters_url=$user.my_url}
                <div class="more_container"
                        data-offsetForMore="{$posts_on_page}"
                        data-noMore="{$no_more_content}"
                        data-pageType="user"
                        data-entityID="{$user.user_id}">
					{if $user_posts}
                        {assign var=no_post_author value=1}
                        {foreach from=$user_posts item=post}
                            {include file='components/front/front_post.tpl' post=$post}
                        {/foreach}
                    {else}
                        {if $my_account}You have{else}{$user.full_name|escape} has{/if} no posts
                    {/if}
                </div>
                <div class="front_loader_div hide"><img src="/images/loading.gif"></div>
            </div>
        </div>
    {/if}

    <div class="right_rail">
        <div class="right_rail_content">
            {include file='components/front/front_right_post_buttons.tpl'}
            {if $user.description || $user.website || $user.facebook || $user.twitter || $user.linkedin}
                <div class="profile_summary">
                    <div class="summary_title">Summary</div>
                    <div class="summary_text">
                        {$user.description|escape|nl2br}
                    </div>
                    {if $user.website}
                        <div class="user_links">
                            <a href="{$user.website}" class="user_website trackedOutboundLink" rel="nofollow">{$user.website}</a>
                            <div class="clear"></div>
                        </div>
                    {/if}
                    {if $user.facebook || $user.twitter || $user.linkedin || $user.google_plus}
                        <div class="user_links">
                            {if $user.facebook}<a href="{$user.facebook}" class="trackedOutboundLink"><img src="/images/icons/facebookicon.png"></a>{/if}
                            {if $user.twitter}<a href="{$user.twitter}" class="trackedOutboundLink"><img src="/images/icons/twittericon.png"></a>{/if}
                            {if $user.linkedin}<a href="{$user.linkedin}" class="trackedOutboundLink"><img src="/images/icons/linkedinicon.png"></a>{/if}
                            {if $user.google_plus}<a href="{$user.google_plus}" class="trackedOutboundLink"><img src="/images/icons/googleplusicon.png"></a>{/if}
                            <div class="clear"></div>
                        </div>
                    {/if}
                </div>
            {/if}
            {if $show_content_type!='connections' && ($user.followers || $user.following)}
                <div class="follows_btn_div">
                    <a class="post_type_link fleft active" id="following_link">Following<span class="divider"> · </span><span id='following_count'>{$user.following|count}</span></a>
                    <a class="post_type_link fleft" id="followers_link">Followers<span class="divider"> · </span><span data-followersCount='{$user.followers|count}' id='followers_count'>{$user.followers|count}</span></a>
                    <div class="clear"></div>
                </div>
                {if $user.following}
                    <div class="profile_summary following">
                        {foreach from=$user.following item=follow name=flw}
                            {if $smarty.foreach.flw.index < $max_follow_icons_number}
                                <a href="{$follow.my_url}" class=""><img class="follow_logo_small" src="{$follow.logo.my_url}"></a>
                            {/if}
                        {/foreach}
                        <div class="clear"></div>
                        <a class="small_link followers_see_all" href="{$user.my_url}/connections?tab_selected=following">See all</a>
                    </div>
                {/if}
                {if $user.followers}
                    <div class="profile_summary followers hide">
                        {foreach from=$user.followers item=follow name=flw}
                            {if $smarty.foreach.flw.index < $max_follow_icons_number}
                                <a href="{$follow.my_url}" class=""><img class="follow_logo_small" src="{$follow.logo.my_url}"></a>
                            {/if}
                        {/foreach}
                        <div class="clear"></div>
                        <a class="small_link followers_see_all" href="{$user.my_url}/connections?tab_selected=followers">See all</a>
                    </div>
                {/if}
            {/if}
        </div>
    </div>
    <div class="clear"></div>
</div>

{include file='components/js_common.tpl'}
<script>
    var tab_selected = '{$tab_selected}';
    var user_id = {$user.user_id};
    $(document).ready(function() {
        setRightRailFixed();
        preparePageForMore();
        prepareContentFilters();
        prepareContentDelete();
        prepareVoting();
        prepareFollowing();
        prepareFollowIconsSwitch();
        prepareSharing();

        if (tab_selected === 'followers') {
            $("#show_followers").click();
        } else if (tab_selected === 'recent') {
            $("#show_recent_connections").click();
        } else {
            $("#show_following").click();
        }


        $(".profile_suspend").click(function() {
            suspendUser($(this).attr('data-isSuspended'));
        });
    });

    function suspendUser(is_suspended) {
        var action = 'suspend';
        if (is_suspended==1) {
            action = 'activate';
        }

        if (!confirm("Confirm you want to "+action+". Are you sure?")) {
            return false;
        }
        $.ajax({
            data: {
                cmd: 'suspendUser',
                user_id: user_id,
                is_suspended: is_suspended
            },
            success: function(data) {
                if (data.status === 'success') {
                    location.reload();
                } else if (data.status === 'failure') {
                    alert(data.message);
                }
            }
        });
    }
</script>

{include file='components/footer_new.tpl'}