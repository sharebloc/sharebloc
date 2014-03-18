<div class="content_container headline_post" data-postType="{$post.post_type}" data-postId="{$post.post_id}" data-entityType="post">
    <div class="headline_post_details">
        <div class="headline_post_header">
            {include file='components/votes_block.tpl'}
            <div class="headline_post_title_div">
                <span class="headline_post_title">
                    <a href="{$post.title_url}" target="_blank" class="trackedOutboundLink">
                        {$post.title|escape|truncate:60:" (...)"}
                    </a>
                </span>
                {if $post.outer_link_host}
                    <div class="clear"></div>
                    <div class="post_outer_link_div"><a href="{$post.outer_link_host}" class="post_outer_link trackedOutboundLink" target="_blank">({$post.outer_link_host})</a></div>
                {/if}
            </div>
            <div class="clear"></div>
        </div>
        <div class="headline_img_div">
            {if $post.logo_url_full}
                <a class="front_image_link trackedOutboundLink" href="{$post.title_url}" target="_blank">
                    <img class="headline_img {if $post.logo_ratio < 1.9}high{/if}" src="{$post.logo_url_src|default:$post.logo_url_full}">
                </a>
            {/if}
        </div>
        <div class="headline_post_data_div">
            <div class="headline_height_sizer">
                {if $post.user.logo}
                    <img class="post_author_icon" src="{$post.user.logo.my_url}">
                {/if}
                <div class="headline_post_author_div">
                    Posted
                    {if empty($no_post_author)}by
                        <span>
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
                    <div class="headline_post_date">
                        {$post.date}
                    </div>
                </div>
                <div class="text_part">
                    {if !empty($post.text)}
                        {$post.text|strip_tags|truncate:215:" (...)":true|nl2br}
                    {/if}
                </div>
            </div>
            <div class="headline_footer">
                <div class="headline_comments_div">
                    <a class="front_comments_link" href="{$post.my_url}" target="_blank">{$post.comment_count} {$post.comments_title}</a>
                </div>
                <div class="post_badges_div">
                    {if $post.subcategories}
                        {foreach from=$post.subcategories item=subcategory}
                            <a class="post_badge subcategory_link" href="{$subcategory.my_url}" target="_blank">{$subcategory.tag_name}</a>
                        {/foreach}
                    {/if}
                    {foreach from=$post.vendor_list item=vendor}
                        <a class="post_badge vendor_link" href={$vendor.my_url} target="_blank">{$vendor.vendor_name|escape}</a>
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>