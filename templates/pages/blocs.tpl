{include file='components/header_new.tpl' active='blocs'}

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

        <div id="following_div" class="content_container blocs_container">
            {if $main_blocs}
                {foreach from=$main_blocs item=follow}
                    {include file='components/front/front_follows.tpl'}
                {/foreach}
            {/if}
            <div class="clear"></div>
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
    $(document).ready(function() {
        $("#submit_custom_invite").click(function() {
            showInvitesPopup();
        });

        prepareFollowing(true);
        prepareCopyToClipboard();
        prepareSharing();
    });
</script>

{include file='components/footer_new.tpl'}