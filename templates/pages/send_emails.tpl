{include file='components/header_new.tpl'}

<div class="page_sizer_wide rails_container">
    {if $show_help}
        {assign var='url_prefix' value=$base_url|cat:"/send_emails.php?type="}
        {assign var='curr_user_part' value="&users="|cat:$current_user_id}
        {assign var='send_email_part' value="&send_emails=1"}
        {assign var='norm_work_part' value="&normal_work=1"}

        <br><br><b>CONTEST launch email for current user:</b><br><br>
        <a target='_blank' href='{$url_prefix}contest_launch{$curr_user_part}'>Current user (show only)</a><br><br>
        <a target='_blank' href='{$url_prefix}contest_launch{$curr_user_part}{$send_email_part}'>Current user (send email)</a><br><br>
        <a target='_blank' href='{$url_prefix}contest_launch{$norm_work_part}'>All users (show only)</a><br><br>
        <hr>

        <br><br><b>CONTEST other 3 emails:</b><br><br>
        <div style="float:left; width: 33%">
            <br>
            Marketo reminder:<br><br>
            <a target='_blank' href='{$url_prefix}contest_marketo_reminder{$curr_user_part}'>Current user (show only)</a><br><br>
            <a target='_blank' href='{$url_prefix}contest_marketo_reminder{$curr_user_part}{$send_email_part}'>Current user (send email)</a><br><br>
            <a target='_blank' href='{$url_prefix}contest_marketo_reminder{$norm_work_part}'>All users (show only)</a><br><br>
        </div>
        <div style="float:left; width: 33%">
            <br>
            Post reminder:<br><br>
            <a target='_blank' href='{$url_prefix}contest_post_reminder{$curr_user_part}'>Current user (show only)</a><br><br>
            <a target='_blank' href='{$url_prefix}contest_post_reminder{$curr_user_part}{$send_email_part}'>Current user (send email)</a><br><br>
            <a target='_blank' href='{$url_prefix}contest_post_reminder{$norm_work_part}'>All users (show only)</a><br><br>
        </div>
        <div style="float:left; width: 33%">
            <br>
            Vote reminder:<br><br>
            <a target='_blank' href='{$url_prefix}contest_vote_reminder{$curr_user_part}'>Current user (show only)</a><br><br>
            <a target='_blank' href='{$url_prefix}contest_vote_reminder{$curr_user_part}{$send_email_part}'>Current user (send email)</a><br><br>
            <a target='_blank' href='{$url_prefix}contest_vote_reminder{$norm_work_part}'>All users (show only)</a><br><br>
        </div>
        <div class="clear"></div>
        <hr>

        <br><br><b>Funnelholic webinar:</b><br><br>
        <a target='_blank' href='{$url_prefix}funnelholic_webinar{$curr_user_part}'>Current user (show only)</a><br><br>
        <a target='_blank' href='{$url_prefix}funnelholic_webinar{$curr_user_part}{$send_email_part}'>Current user (send email)</a><br><br>
        <a target='_blank' href='{$url_prefix}funnelholic_webinar{$norm_work_part}'>All users (show only)</a><br><br>
        <hr>

         <br><br><b>CONTEST ending email for current user:</b><br><br>
        <a target='_blank' href='{$url_prefix}marketo_contest_end{$curr_user_part}'>Current user (show only)</a><br><br>
        <a target='_blank' href='{$url_prefix}marketo_contest_end{$curr_user_part}{$send_email_part}'>Current user (send email)</a><br><br>
        <a target='_blank' href='{$url_prefix}marketo_contest_end{$norm_work_part}'>All users (show only)</a><br><br>
        <hr>


        <br><br><b>Testing weekly email:</b><br><br>
        <a target='_blank' href='{$url_prefix}weekly{$curr_user_part}'>Current user (show only)</a><br><br>
        <a target='_blank' href='{$url_prefix}weekly{$curr_user_part}{$send_email_part}'>Current user (send email)</a><br><br>
        <a target='_blank' href='{$url_prefix}weekly{$norm_work_part}'>All users (show only)</a><br><br>
        <hr>

        <br><br><b>Testing daily email:</b><br><br>
        <a target='_blank' href='{$url_prefix}daily{$curr_user_part}'>Current user (show only)</a><br><br>
        <a target='_blank' href='{$url_prefix}daily{$curr_user_part}{$send_email_part}'>Current user (send email)</a><br><br>
        <a target='_blank' href='{$url_prefix}daily{$norm_work_part}'>All users (show only)</a><br><br>
        <hr>

      

        <br><br><b>Testing blocs emails for unregistered:</b><br><br>
        {foreach $subscription_blocs item=tag_id}
            <div style="float:left; width: {100/count($subscription_blocs)}%">
                <br>
                {$vendor_category_list.$tag_id.tag_name}:<br><br>
                <a target='_blank' href='{$url_prefix}bloc_feed_email{$curr_user_part}&bloc_id={$tag_id}'>Current user (show only)</a><br><br>
                <a target='_blank' href='{$url_prefix}bloc_feed_email{$curr_user_part}{$send_email_part}&bloc_id={$tag_id}'>Current user (send email to current user)</a><br><br>
                <a target='_blank' href='{$url_prefix}bloc_feed_email{$norm_work_part}&bloc_id={$tag_id}'>All users (show only)</a><br><br>
            </div>
        {/foreach}
        <div class="clear"></div>
        <hr>

        <br><br><b>Testing current onetime email for current user:</b><br><br>
        <a target='_blank' href='{$url_prefix}onetime{$curr_user_part}'>Current user (show only)</a><br><br>
        <a target='_blank' href='{$url_prefix}onetime{$curr_user_part}{$send_email_part}'>Current user (send email)</a><br><br>
        <a target='_blank' href='{$url_prefix}onetime{$norm_work_part}'>All users (show only)</a><br><br>
        <hr>

        <br><br><b>Testing daily emails when your friends joins or follows you:</b><br><br>
        <a target='_blank' href='{$url_prefix}friends_join_daily{$curr_user_part}'>Current user (show only)</a><br><br>
        <a target='_blank' href='{$url_prefix}friends_join_daily{$curr_user_part}{$send_email_part}'>Current user (send email)</a><br><br>
        <a target='_blank' href='{$url_prefix}friends_join_daily{$norm_work_part}'>All users (show only)</a><br><br>
        <hr>

        <br><br><b>Testing daily emails to the auto-post publishers:</b><br><br>
        <a target='_blank' href='{$url_prefix}publishers_info{$curr_user_part}'>Current user (show only)</a><br><br>
        <a target='_blank' href='{$url_prefix}publishers_info{$curr_user_part}{$send_email_part}'>Current user (send email)</a><br><br>
        <a target='_blank' href='{$url_prefix}publishers_info{$norm_work_part}'>All users (show only)</a><br><br>
        <hr>

        <br><br><b>Testing weekly emails to suggest users to follow:</b><br><br>
        <a target='_blank' href='{$url_prefix}suggestion_weekly{$curr_user_part}'>Current user (show only)</a><br><br>
        <a target='_blank' href='{$url_prefix}suggestion_weekly{$curr_user_part}{$send_email_part}'>Current user (send email)</a><br><br>
        <a target='_blank' href='{$url_prefix}suggestion_weekly{$norm_work_part}'>All users (show only)</a><br><br>
        <hr>
    {/if}
</div>
{include file='components/js_common.tpl'}
<script>
    $(document).ready(function() {
        if (show_invite_popup) {
            $("#invites_link").click();
        }

        $("#welcome_popup_close").click(function(){
            $("#popup_welcome_container").hide();
            return false;
        });
    });
</script>