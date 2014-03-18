<div id="popup_nominate_contest" class="standard_popup_container hide">
    <div class="standard_popup">
        <div id="nominate_contest_title" class="title_wide_popup">{if empty($f_post_comment)}Join to Complete Your Nomination{else}Join to Post Your Comment{/if}</div>
        <div id="nominate_contest_success" class="standard_popup_content">
            {if empty($f_post_comment)}
                We require all nominations to join so we can proudly display your profile in the nomination post.
            {else}
                Please join to have your comment posted.
            {/if}
            <br><br>It'll only take a minute.
        </div>
        <div class="popup_function">
            <a id="nominate_contest_join" class="save_changes" data-entityType="{if empty($f_post_comment)}post{else}comment{/if}" href="#">Join</a>
            <a id="nominate_contest_popup_close" class="cancel" href="#">Cancel</a>
        </div>
    </div>
</div>