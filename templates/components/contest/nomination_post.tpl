<div id="{$post.uid}" data-postType="{$post.post_type}"
     data-postId="{$post.post_id}" data-entityType="post" class="nomin_post post_container">
    <div class="nomin_post_left_div">
        <div class='nomin_post_number'>{$post.entity_number}</div>
        <div class="nomin_post_votes_block">
            {include file='components/votes_block.tpl'}
            <div class="clear"></div>
        </div>
        <!--div class="clear"></div-->
    </div>

    <div id="{$post.uid}_main" class="nomin_post_right_div">
        <div class="nomin_post_title_div">
            <a href="{$post.my_url}" target="_blank" class="trackedOutboundLink">
                {$post.title|escape|truncate:55:"...":true}
            </a>
        </div>
        <div class="nomin_post_by_div">
            Nominated by
            {if $post.user.my_url}
                <a href="{$post.user.my_url}" target="_blank">{$post.user.short_name|escape}</a>
            {else}
                {$post.user.short_name|escape}
            {/if}
            {if $post.subcategories && $contest_id <> 2}
                to <a class="post_outer_link" href="{$post.subcategories[0].my_url}" target="_blank">{$post.subcategories[0].tag_name}</a>
            {/if}
        </div>
    </div>
    {if $post.can_delete}
        <div class="post_delete_btn_div delete_btn" data-entityType="post">x</div>
    {/if}
    <div class="clear"></div>
</div>