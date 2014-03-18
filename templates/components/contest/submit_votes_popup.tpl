<div id="popup_submit_votes_container" class="standard_popup_container {if !$just_confirmed_votes}hide{/if}">
    <div class="standard_popup">
        <div class="title_wide_popup">
            {if $just_confirmed_votes}
                Your votes are in!
            {else}
                Thanks for voting
            {/if}
        </div>
        <div id="submit_votes_form_div" class="standard_popup_content {if $just_confirmed_votes || $logged_in || $contest_voter}hide{/if}">
            <form id="submit_votes_form">
                <div class="popup_input_row">
                    Submit your name and email to confirm your vote.
                </div>
                <div id="contest_first_name" class="popup_input_row">
                    <input id="submit_votes_first_name" name="first_name" class="validate[required,maxSize[64]] textbox join_input" placeholder="First Name" value=""/>
                    <div class="join_field_status">
                        <img class="join_field_status_img" src="/images/input_ok.png">
                    </div>
                </div>
                <div id="contest_last_name" class="popup_input_row">
                    <input id="submit_votes_last_name" name="last_name" class="validate[required,maxSize[64]] textbox join_input" placeholder="Last Name" value=""/>
                    <div class="join_field_status">
                        <img class="join_field_status_img" src="/images/input_ok.png">
                    </div>
                </div>
                <div id="contest_email" class="popup_input_row">
                    <input id="submit_votes_email" name="email" class="validate[custom[email],required,maxSize[128]] textbox join_input" placeholder="Add Email" value=""/>
                    <div class="join_field_status">
                        <img class="join_field_status_img" src="/images/input_ok.png">
                    </div>
                </div>
                <div class="popup_input_row">
                    You can vote three times each day.
                </div>
                <div id="email_exists" class="popup_input_row">
                    Already a ShareBloc User? <a href='/signin'>Sign in here to vote.</a>
                </div>
            </form>
        </div>
        <div id="submit_votes_success" class="standard_popup_content {if !$just_confirmed_votes && !$logged_in && !$contest_voter}hide{/if}">
            {if $just_confirmed_votes || $logged_in || $contest_voter}
                Help spread the world on the best marketing automation posts by sharing on Twitter and social media.
                {if $logged_in && $user_info.f_get_sponsor_email == 0}
                    <br><br>
                    <input id="get_sponsor_email_chk_thanks" class="post_no_thumb_chk" type="checkbox" name="" checked="checked">
                    <span class="">Opt into one promotional email from each of our sponsors?</span>
                {/if}
            {else}
                Thank you for voting. We just sent you an email. Click on the link in the email to confirm your vote.
                <br><br>
                Remember you can vote up to three times each day.
            {/if}
            <br><br>
            <div id="submit_votes_tweet_btn" data-provider="twitter" data-text="Come vote on Content Marketing Nation Contest #cntmktgnation14 on @ShareBloc"
                 class="share_post_btn fleft" data-shareUrl="{$base_url}/{$contest_url}">
                <img class="tweet_img" src="/images/twitter.png"/>
                Tweet
            </div>
            <a class="cancel_btn submit_votes_close_btn fright" href="#">Close</a>
            <div class="clear"></div>
        </div>
        <div id="submit_votes_buttons_div" class="popup_function {if $just_confirmed_votes || $logged_in || $contest_voter}hide{/if}">
            <a id="submit_votes_send" class="save_changes" href="#">Submit</a>
            <a class="cancel_btn cancel" href="#">Cancel</a>
        </div>
    </div>
</div>