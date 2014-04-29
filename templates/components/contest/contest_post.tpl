<div id="{$post.uid}" data-postType="{$post.post_type}" data-postId="{$post.post_id}" data-entityType="post" class="post_container contest_post {if empty($for_show_post) && ($post.entity_number>50)}below_top{/if}">
    <div class="contest_post_left_div">
        {if empty($for_show_post)}
            <div class='contest_post_number'>{$post.entity_number}.</div>
        {/if}
        {include file='components/votes_block.tpl'}
        <div class="clear"></div>
        {if $post.can_delete}
            <div class="post_delete_btn_div delete_btn" data-entityType="post">x</div>
        {/if}
    </div>
    <div id="{$post.uid}_main" class="post_main_div">
        <div class="post_header_div">
            {if $post.logo_url_full}
                <div class="front_image_div" style="background-image: url('{$post.logo_url_full}')">
                    <a class="front_image_link trackedOutboundLink" href="{$post.title_url}" target="_blank"><img class="front_image" src="{$post.logo_url_full}" alt="{$post.title|escape}"></a>
                </div>
            {/if}
            <div class="post_details {if !$post.logo_url_full}wide{/if}">
                <div class="post_title_div">
                    <span class="post_title">
                        <a href="{$post.title_url}" target="_blank" class="trackedOutboundLink" rel="nofollow">
                            {$post.title|escape}
                        </a>
                    </span>
                    <div class="subdetails">
                        {if !empty($post.author_name)}
                            By {$post.author_name}
                        {/if}
                        {if $post.outer_link_host}
                            {if !empty($post.author_name)}on{else}On{/if}
                            <a href="{$post.outer_link_host}" class="post_outer_link trackedOutboundLink" target="_blank" rel="nofollow">{$post.outer_link_host}</a>
                        {/if}
                        {if $post.subcategories}
                            to
                            <span><a class="post_outer_link" href="{$post.subcategories[0].my_url}" target="_blank">{$post.subcategories[0].tag_name}</a></span>
                            {/if}
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="clear"></div>
        <div class="nominated_by_div">
            {if !empty($post.user.logo.my_url)}
                <a href="{$post.user.my_url}" target="_blank"><img class="contest_author_logo" src="{$post.user.logo.my_url}" alt="{$post.user.full_name|escape}"></a>
            {/if}
            Nominated by

            {if $post.user.my_url}
                <a class="nominated_author_link" href="{$post.user.my_url}" target="_blank">{$post.user.full_name|escape}</a>{if !empty($post.user.company.name)}, {/if}
            {else}
                {$post.user.full_name|escape}{if !empty($post.user.company.name)}, {/if}
            {/if}
            {if !empty($post.user.company.name)}
                <a class="nominated_link" href="{$post.user.company.my_url}">{$post.user.company.name}</a>
            {/if}

            {if !empty($post.user.facebook) || !empty($post.user.twitter) || !empty($post.user.linkedin) || !empty($post.user.google_plus)}
                <span class="user_links">
                    {if !empty($post.user.facebook)}<a href="{$post.user.facebook}" class="trackedOutboundLink" rel="nofollow"><img src="/images/icons/facebookicon.png" alt="Facebook"></a>{/if}
                    {if !empty($post.user.twitter)}<a href="{$post.user.twitter}" class="trackedOutboundLink" rel="nofollow"><img src="/images/icons/twittericon.png" alt="Twitter"></a>{/if}
                    {if !empty($post.user.linkedin)}<a href="{$post.user.linkedin}" class="trackedOutboundLink" rel="nofollow"><img src="/images/icons/linkedinicon.png" alt="LinkedIn"></a>{/if}
                    {if !empty($post.user.google_plus)}<a href="{$post.user.google_plus}" class="trackedOutboundLink" rel="nofollow"><img src="/images/icons/googleplusicon.png" alt="Google+"></a>{/if}
                </span>
                <div class="clear"></div>
            {/if}
        </div>
        {if $post.text}
            <div class="post_footer_div trackedOutboundDiv">
                {$post.text|nl2br}
            </div>
        {/if}
        {if empty($for_show_post)}
            <div class="post_footer_div">
                <div class="fleft">
                    {if $post.comment_count > 0}
                        <a class="comments_link tweet_link" href="{$post.my_url}" target="_blank">{$post.comment_count} {$post.comments_title}</a>
                    {else}
                        <a class="comments_link" href="{$post.my_url}" target="_blank">Add a comment</a>
                    {/if}
                </div>
                {if $post.entity_number<=50}
                    <div class="share_links">
                        {assign var='twitter_text_truncated' value=$post.title|escape|truncate:$twitter_symbols_left:"...":true}
                        {assign var='text_to_share_email' value="Congratulations to "|cat:$post.title|escape|cat:" for being a #2013Top50ContentMarketing winner via @ShareBloc"}
                        {assign var='text_to_share_twitter' value="Congratulations to "|cat:$twitter_text_truncated|cat:" for being a #2013Top50ContentMarketing winner via @ShareBloc"}
                        <a class="comments_link tweet_link share_post_btn" data-text="{$text_to_share_twitter}" data-provider="twitter" data-shareUrl="{$base_url}{$post.my_url_share}">
                            <img class="tweet_img" src="/images/twitter.png" alt="Tweet">
                            Tweet
                        </a>
                        <a class="comments_link share_post_btn" data-text="{$text_to_share_email}" data-provider="mail" data-shareUrl="{$base_url}{$post.my_url_share}">Share</a>
                    </div>
                {/if}
                <div class="clear"></div>
            </div>
        {else}
            <div class="post_badges_div">
                <a class="small_btn reply_btn" data-entityType="post" href="#">Reply</a>
            </div>
            {* place for comment_input_div*}
        {/if}

    </div>
    <div class="clear"></div>
</div>