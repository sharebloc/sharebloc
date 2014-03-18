<div id="popup_repost_container" class="standard_popup_container">
    <div class="standard_popup repost_popup">
        <div class="title_wide_popup">
            {if $reposted_popup_type == 'auto'}
                This link was already posted!
            {else}
                Reposted!
            {/if}
        </div>
        <div class="standard_popup_content">
                <div class="popup_text_div">
                    {if $reposted_popup_type == 'auto'}
                        This link has already been posted so we have reposted it into your profile.
                    {else}
                        This post has been reposted into your profile so your followers will see it in their feed.
                    {/if}
                </div>
        </div>
        {assign var='text_to_share_twitter' value=$post_data.title|escape|truncate:$twitter_symbols_left:"...":true}
        <div class="share_post_btn share_and_tweet_btn" data-provider="twitter" data-text="{$text_to_share_twitter|cat:" via @ShareBloc"}" data-shareUrl="{$base_url}{$post_data.my_url}"><img class="tweet_img" src="/images/twitter.png">Tweet this post</div>
        <a id="repost_type_popup_close" class="tweet_type_popup_close" href="#">Close</a>
        <div class="clear"></div>
    </div>
</div>