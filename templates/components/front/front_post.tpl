<div id="{$post.uid}" data-postType="{$post.post_type}" data-postId="{$post.post_id}" data-entityType="post" class="post_container">
    {include file='components/votes_block.tpl'}
    <div id="{$post.uid}_main" class="post_main_div">
        <div class="post_header_div">
            {if $post.logo_url_full}
                <div class="front_image_div" style="background-image: url('{$post.logo_url_full}')">
                    <a class="front_image_link trackedOutboundLink" href="{$post.title_url}" rel="nofollow" target="_blank"></a>
                </div>
            {/if}
            <div class="post_details">
                <div class="post_title_div">
                    <span class="post_title">
                        <a href="{$post.title_url}" target="_blank" class="trackedOutboundLink" rel="nofollow">
                            {if empty($for_show_post)}
                                {$post.title|escape|truncate:100:" (...)"}
                            {else}
                                {$post.title|escape}
                            {/if}
                        </a>
                    </span>
                    {if $post.outer_link_host}
                        <div class="post_outer_link_div"><a href="{$post.outer_link_host}" class="post_outer_link trackedOutboundLink" target="_blank" rel="nofollow">({$post.outer_link_host})</a></div>
                    {/if}
                    <div class="post_badges_div">
                        {if $post.categories}
                            <a class="post_badge subcategory_link" href="{$post.categories[0].my_url}" target="_blank">{$post.categories[0].tag_name}</a>
                        {/if}
                        {if $post.subcategories}
                            <a class="post_badge subcategory_link" href="{$post.subcategories[0].my_url}" target="_blank">{$post.subcategories[0].tag_name}</a>
                        {/if}
                        {foreach from=$post.vendor_list item=vendor}
                            <a class="post_badge vendor_link" href={$vendor.my_url} target="_blank">{$vendor.vendor_name|escape}</a>
                        {/foreach}
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
            <div class="post_author_div">
                {if !empty($post.user.logo.my_url_thumb)}
                    <span class="post_author_logo_div">
                        <img class="post_author_logo" src="{$post.user.logo.my_url_thumb}">
                    </span>
                {/if}
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
                {if $post.f_auto}
                    <span class="">{if $post.author_vendor_id}[Auto]{else}[Publisher]{/if}</span>
                {/if}
                on {$post.date}
                {if !empty($for_show_post)}
                    <span class="divider"> &middot; </span>
                    <a class="front_comments_link" href="{$post.my_url}" target="_blank">{$post.comment_count} {$post.comments_title}</a>
                {/if}
            </div>
            <div class="clear"></div>
            {if $post.can_delete}
                <div class="post_delete_btn_div delete_btn" data-entityType="post">x</div>
            {/if}
        </div>
        <div class="clear"></div>
        {if $post.text}
            <div class="post_footer_div trackedOutboundDiv">
                {if empty($for_show_post)}
                    {$post.text|strip_tags|truncate:250:" (...)"|nl2br}
                {else}
                    {$post.text|nl2br}
                {/if}
            </div>
        {/if}
        <div class="post_footer_div">
            <div class="">
                <div class="fleft">
                    <span class="share_span">
                        {assign var='text_to_share_twitter' value=$post.title|escape|truncate:$twitter_symbols_left:"...":true}
                        {if $use_contest_vote}
                            <a class="comments_link tweet_link share_post_btn no_padding" data-text="Please help vote for {$text_to_share_twitter|cat:" on @ShareBloc #cntmktgnation14"} " data-provider="twitter" data-shareUrl="{$base_url}{$post.my_url_share}">
                            <img class="tweet_img" src="/images/twitter.png" alt="Tweet">
                                Tweet
                            </a>
                        {else}
                            <a class="comments_link tweet_link share_post_btn no_padding" data-text="{$text_to_share_twitter|cat:" via @ShareBloc"}" data-provider="twitter" data-shareUrl="{$base_url}{$post.title_url}" rel="nofollow">
                                <img class="tweet_img" src="/images/twitter.png" alt="Tweet">
                                Tweet
                            </a>
                        {/if}
                        {if !$use_contest_vote}
                            <a class="comments_link repost_link {if $post.reposted_by_curr_user || $post.curr_user_is_author}reposted{/if}" href="#">
                                {if $post.reposted_by_curr_user}
                                    Reposted
                                {elseif $post.curr_user_is_author}
                                    {if $post.reposters}
                                    Reposts
                                    {/if}
                                {else}
                                    Repost
                                {/if}
                                {if $post.reposters}
                                    ({$post.reposters|count})
                                {/if}
                            </a>
                        {/if}
                    </span>
                </div>
                {if empty($for_show_post)}
                    <div class="fright">
                        <span class="comments_authors_span">
                            {if $post.comment_count > 0}
                                <a class="comments_link" href="{$post.my_url}" target="_blank">{$post.comments_authors|count} discussing</a>
                                {foreach $post.comments_authors_limited item = user}
                                    {if !empty($user.logo.my_url_thumb)}
                                        <img class="comment_author_logo" src="{$user.logo.my_url_thumb}">
                                    {/if}
                                {/foreach}
                            {else}
                                <a class="comments_link" href="{$post.my_url}" target="_blank">Discuss</a>
                            {/if}
                        </span>
                    </div>
                {/if}
                <div class="clear"></div>
            </div>
            {if $post.is_sponsored}
                <div class="fright"><img class="outer_link_img" src="/images/outer_link.png"/>&nbsp;Sponsored</div>
            {/if}
            <div class="clear"></div>
        </div>
        {if !empty($for_show_post)}
            <div class="post_badges_div">
                <a class="small_btn reply_btn" data-entityType="post" href="#">Reply</a>
            </div>
        {/if}

        {* place for comment_input_div*}

    </div>
    <div class="clear"></div>
</div>