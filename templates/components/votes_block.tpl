<div id="vote_{$post.uid}" class="post_vote_div">
    <div class="arrow_up {if $post.vote.user_vote>0}arrow_inactive{else}arrow_active{/if}" data-postType="{$post.post_type}" data-postId="{$post.post_id}" data-voteValue="{$user_info.upvote_value}"></div>
    <div class="vote_value" id="{$post.post_type}_vote_total_{$post.post_id}">{if $post.vote.total < 0}0{else}{$post.vote.total}{/if}</div>{$post.views}
    <div class="arrow_down {if $post.hide_downvote}hide{/if} {if $post.f_my || $post.vote.user_vote<0}arrow_inactive{else}arrow_active{/if}" data-postType="{$post.post_type}" data-postId="{$post.post_id}" data-voteValue="-1"></div>
</div>