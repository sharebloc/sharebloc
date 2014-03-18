<div id="{$comment.comment_id}" data-commentId="{$comment.comment_id}" data-postType="{$comment.post_type}" data-postId="{$post.post_id}" data-entityType="comment" class="comment_container">
    <a name="comment_{$comment.comment_id}"></a>
    {* todo use votes_bloc.tpl *}
    <div id="vote_comment_{$comment.comment_id}" class="comment_vote_div">
        <div class="arrow_up {if $comment.vote.user_vote>0}arrow_inactive{else}arrow_active{/if}" data-entityType="comment" data-commentId="{$comment.comment_id}" data-voteValue="1"></div>
        <div class="vote_value" id="comment_vote_total_{$comment.comment_id}">{if $comment.vote.total < 0}0{else}{$comment.vote.total}{/if}</div>
        <div class="arrow_down {if $comment.hide_downvote}hide{/if} {if $comment.f_my || $comment.vote.user_vote<0}arrow_inactive{else}arrow_active{/if}" data-entityType="comment" data-commentId="{$comment.comment_id}" data-voteValue="-1"></div>
    </div>
    <div id="{$comment.comment_id}_main" data-commentID="{$comment.comment_id}" class="comment_main_div">
        <div class="comment_header_div">
            <div class="comment_author_div">
                <span>
                    {if $comment.user.my_url}
                        <a class="comment_author_link" href="{$comment.user.my_url}">{$comment.user.full_name|escape}</a>
                    {else}
                        <span class="comment_author_link">{$comment.user.full_name|escape}</span>
                    {/if}
                </span>
                <span class="comment_date">{$comment.date}</span>
            </div>
            {if $comment.can_delete}
                <div class="comment_delete_btn_div delete_btn" data-entityType="comment">x</div>
            {/if}
        </div>
        <div class="clear"></div>
        <div class="comment_text_div trackedOutboundDiv">
            {$comment.comment_text|nl2br}
        </div>
        <div class="comment_footer_div">
            <div class="fleft"><a href="#" data-entityType="comment" class="comment_action_link reply_btn">Reply</a></div>
            <div class="fleft"><a href="{$post.my_url}#comment_{$comment.comment_id}" class="comment_action_link" target="_blank">Permalink</a></div>
            <div class="clear"></div>
        </div>

        {* place for comment_input_div*}

    </div>
    <div class="clear"></div>
</div>