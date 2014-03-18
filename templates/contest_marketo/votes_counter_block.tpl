<div class="contest_votes_block {if !empty($for_show_post)}inside_show_post{elseif !empty($for_nominations_page)}inside_nominations_page{/if}">
    <div class="page_sizer_wide">
        <a href="/{$contest_url}" target="_blank"><img src="/images/contest widget.png" class="fleft contest_widget_img"></a>
        <div class="votes_counter_div fright {if $contest_votes_left<1}hide{/if}">
            <span class="votes_left_count">
                <span class="votes_left_count_number">
                    {$contest_votes_left} vote{if $contest_votes_left!==1}s{/if}
                </span> remaining today.
            </span>
            <span class="contest_end_text {if !empty($for_show_post) || !empty($for_nominations_page)}at_right{/if}">
                Contest ends on March 28.
            </span>
        </div>
        <div class="votes_submit_div votes_left_count fright {if $contest_votes_left>=1}hide{/if}">
            {if $logged_in || $contest_voter}
                Thanks for voting!
            {else}
                0 votes remaining.
                <span class='votes_submit_link'>Click to submit.</span>
            {/if}
            <span class="contest_end_text {if !empty($for_show_post) || !empty($for_nominations_page)}at_right{/if}">
                Contest ends on March 28.
            </span>
        </div>
        <div class="clear"></div>
    </div>



</div>

{assign var="just_confirmed_votes" value=$just_confirmed_votes|default:0}
{include file='components/contest/submit_votes_popup.tpl'}
