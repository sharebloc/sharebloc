{include file='components/mailer/header.tpl' title="{$subject}"}

{$addressee_first_name},<br>
{if $reminder_type=='contest_marketo_reminder'}
    Be sure to check out the <a href="{$base_url}/{$contest_url}" target="_blank">Content Marketing Nation Contest</a> to win a FREE ticket to <a href="http://summit.marketo.com/2014/" target="_blank">The Marketing Nation Summit</a> from April 7-9. We want to find out who has the best post about marketing automation.
{else if $reminder_type=='contest_post_reminder'}
    Thanks for posting! Remember to get your colleagues to keep voting for the best marketing automation content to help you win a FREE ticket to <a href="http://summit.marketo.com/2014/" target="_blank">The Marketing Nation Summit</a> from April 7-9.
{else if $reminder_type=='contest_vote_reminder'}
    Don't forget to keep voting for the best marketing automation content to help someone win a FREE ticket to <a href="http://summit.marketo.com/2014/" target="_blank">The Marketing Nation Summit</a> from April 7-9.
{/if}

<br>
<a href="{$base_url}/{$contest_url}" target="_blank">
    <img src="{$base_url}/images/marketo_mini.png" style="border:1px solid #cacaca; margin: 10px 0;">
</a>
<br>


{if $reminder_type=='contest_marketo_reminder'}
    The post with the most votes on the <a href="{$base_url}/{$contest_url}" target="_blank">Content Marketing Nation Contest</a> will be the winner. Remember, you can post as often as you'd like and vote up to three times a day.
{else if $reminder_type=='contest_post_reminder'}
    The winner will be chosen by whomever has the most votes for the <a href="{$base_url}/{$contest_url}" target="_blank">Content Marketing Nation Contest</a>. Remember, you and your colleagues can vote up to three times a day.
{else if $reminder_type=='contest_vote_reminder'}
    Your votes for the <a href="{$base_url}/{$contest_url}" target="_blank">Content Marketing Nation Contest</a> will help pick a winner. Remember, you can vote up to three times a day.
{/if}


<br><br>
Cheers,<br>
<a href='{$base_url}/users/david_cheng'>David Cheng</a> & <a href='{$base_url}/users/andrew_koller'>Andrew Koller</a>
<br>
Co-founders of <a href='{$base_url}'>ShareBloc</a>
<br><br>

<br><br>
<span style="font-size:10px;"><a href="{$base_url}/unsubscribe/updates/{$unsubscribe_key}" target="_blank">Unsubscribe</a> or <a href="{$base_url}{$user_url}/account?active_tab=notifications_tab" target="_blank">Manage your email preferences</a></span>

{include file='components/mailer/footer.tpl'}