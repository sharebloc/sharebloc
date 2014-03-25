<div class="contest_votes_block {if !empty($for_show_post)}inside_show_post{elseif !empty($for_nominations_page)}inside_nominations_page{/if}">
    <div class="votes_thanks_div">
        {if !empty($for_show_post) || !empty($for_nominations_page)}
            <span class="back_to_contest_div">
                <a href="/{$contest_url}">&larr;&nbsp;Back to contest</a>
            </span>
        {/if}
        <span class="contest_end_text">
            Thanks for voting. The contest is over.
        </span>
    </div>
</div>

{include file='contest_marketo/thank_you_popup.tpl'}
