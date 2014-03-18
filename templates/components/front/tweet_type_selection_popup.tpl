<div id="popup_tweet_type_selection" class="standard_popup_container hide">
    <div class="standard_popup">
        <div class="title_wide_popup">
            {if $use_contest_vote}
                Thanks for posting
            {else}
                Tweet this post
            {/if}
        </div>
        <div id="nominate_contest_success" class="standard_popup_content">
            {if $use_contest_vote}
                To get the most out of your post, we suggest you to share it on Twitter and social media.
            {else}
                Thanks for sharing this on ShareBloc. Would you like to share this also on Twitter?
            {/if}
        </div>
        <div class="share_post_btn share_and_tweet_btn" data-provider="twitter" data-text="{$text_to_share_twitter}" data-shareUrl="{$base_url}{$url_to_share}"><img class="tweet_img" src="/images/twitter.png">Tweet this post</div>
        {if !$use_contest_vote}
            <div class="share_post_btn share_and_tweet_btn" data-provider="twitter" data-text="{$text_to_share_twitter}" data-shareUrl="{$post_data.title_url}"><img class="tweet_img" src="/images/twitter.png">Tweet the original content</div>
        {/if}
        <a id="tweet_type_popup_close" class="tweet_type_popup_close" href="#">Close</a>
        <div class="clear"></div>
    </div>
</div>