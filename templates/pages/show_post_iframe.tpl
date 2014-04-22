{include file='components/header_new.tpl'}
{assign var='post' value=$post_data}
<div class="iframe_page_container">
    <div id="{$post.uid}" data-postType="{$post.post_type}" data-postId="{$post.post_id}" data-entityType="post" class="post_container iframe_post">
        {include file='components/votes_block.tpl'}
        <div id="{$post.uid}_main" class="post_main_div">
            {if !empty($post.user.logo.my_url_thumb)}
                <div class="fleft">
                    <img class="post_author_logo" src="{$post.user.logo.my_url_thumb}">
                </div>
            {/if}
            <div class="iframe_post_div">
                <div class="iframe_post_header">
                    Posted
                    {if empty($no_post_author)}by
                        <span class="{if $is_admin && $post.user.status=='inactive'}inactive_user_name{/if}">
                            {if $post.user.my_url}
                                <a class="post_author_link" href="{$post.user.my_url}" target="_blank">{$post.user.full_name|escape}</a>
                            {else}
                                {$post.user.full_name|escape}
                            {/if}
                        </span>
                    {/if}
                    {if isset($post.user.f_elite) && $post.user.f_elite}
                        <span class="elite">Elite</span>
                    {/if}
                    <a id="iframe_close_lnk" href="{$post.url}" class="iframe_close_lnk">Remove Frame X </a>
                    {if $logged_in}
                        <a class="back_to_feed_link" href="/">Back to Feed</a>
                    {/if}
                </div>
                <div>
                    {assign var='text_to_share_twitter' value=$post.title|escape|truncate:$twitter_symbols_left:"...":true}
                    <a id="tweet_btn" class="comments_link tweet_link share_post_btn no_padding" data-text="{$text_to_share_twitter|cat:" via @ShareBloc"}" data-provider="twitter" data-shareUrl="{$base_url}{$post.title_url}">
                        <img class="tweet_img" src="/images/twitter.png" alt="Tweet">
                        Tweet
                    </a>               
                    <a class="comments_link" href="{$post.my_url}" target="_blank">Discuss
                        {if $post.comment_count > 0}({$post.comment_count}){/if}
                    </a>
                    <span class="iframe_views">
                    {$post.views}
                    {if $post.views == 1}
                        View
                    {else}
                        Views
                    {/if}
                    </span>
                </div>
            </div>
        </div>
        <div class="iframe_logo_sharebloc">
            <a href="/">
                <img class="vslogo_header" src="/images/sharebloc_logo.png" alt="ShareBloc">
            </a>
        </div>
        {if $contest_id == 2}
            <div class="fright">
                <a href="{$base_url}/{$contest_url}" target="_blank">
                    <img class="iframe_contest_marketo_logo" src="/images/contest_marketo_widget_black.png" alt="Content Marketing Nation Contest">
                </a>
            </div>
        {/if}
        <div class="clear"></div>
    </div>
    <iframe frameborder="0" height="100%" marginheight="0" marginwidth="0" src="{$post.url}"></iframe>
</div>
{if !empty($reposted_popup_type)}
    {include file='components/front/repost_popup.tpl'}
{/if}

{include file='components/js_common.tpl'}
<script>
    var login_redirect_url = "{$login_redir_path}";
    var pressed_reply_button = null;
    var tweet_after_post = {$tweet_after_post};
    var post_type = "{$post_data.post_type}";
    var post_id = "{$post_data.post_id}";
    var loading = false;

    $(document).ready(function() {
        $("#repost_type_popup_close").click(function(){
            $("#popup_repost_container").hide();
            return false;
        });

        prepareVoting();
        prepareSharing();
        prepareRepost();

        if (tweet_after_post) {
            $("#tweet_btn").click();
        }

        $("#popup_repost_container .share_post_btn").click(function() {
            $("#popup_repost_container").hide();
            return true;
        });

    });

</script>

{include file='components/footer_new.tpl'}