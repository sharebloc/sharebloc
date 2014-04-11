{include file='components/header_new.tpl' active='home'}

<div class="page_sizer_wide rails_container">
    <div class="left_rail">
        {if !$logged_in}
            <div class="post_title_container">
                <div class="post_title_title">ShareBloc is a platform for like-minded professionals to share, curate and discuss business content that matters.</div>
                <div class="post_title_text">Sign up to get a feed that is curated specifically to your professional interests.</div>
                <div class="post_title_text">
                    <a href="{$join_redir_path}" class="post_create_acc_btn" target="_blank">Create a free account</a>
                </div>
            </div>
        {/if}

        {include file='components/front/front_filters_block.tpl'}

        <div class="content_container">
            {include file='components/front/front_type_filters_line.tpl'}
            <div class="more_container"
                data-offsetForMore="{$posts_on_page}"
                data-noMore="{$no_more_content}"
                data-pageType="feed"
                data-entityID="0">
                {foreach from=$content item=front_post}
                    {include file='components/front/front_post.tpl' post=$front_post}
                {/foreach}
            </div>
            <div class="front_loader_div hide"><img src="/images/loading.gif"></div>
        </div>
    </div>
    <div class="right_rail">
        <div class="right_rail_content">
             {include file='components/front/front_right_post_buttons.tpl'}
            <!--AK 2014-04-10 removed contest widget
            {if $show_contest_widget}
                {include file='contest_marketo/contest_widget_block.tpl'}
            {/if} -->
            {include file='components/front/invite_link.tpl' type='user'}
        </div>
    </div>
    <div class="clear"></div>
</div>
{if $show_join_welcome_popup}
    {include file='components/welcome_popup.tpl'}
{/if}
{include file='components/js_common.tpl'}
<script>
    var show_invite_popup = {$show_invite_popup};
    $(document).ready(function() {
        if (show_invite_popup) {
            $("#invites_link").click();
        }

        $("#welcome_popup_close").click(function(){
            $("#popup_welcome_container").hide();
            return false;
        });

        setRightRailFixed();
        preparePageForMore();
        prepareContentFilters();
        prepareContentDelete();
        prepareVoting();
        prepareCopyToClipboard();
        prepareSharing();
        prepareRepost();
    });
</script>

{include file='components/footer_new.tpl'}