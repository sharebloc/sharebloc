{include file='components/mailer/joined_person_function.tpl'}

{include file='components/mailer/header.tpl' title="{$subject}"}
<br>
{$addressee_first_name},<br>
Based on your preferences, you might also be interested in following these people.

<table style="font-size:13px;
              margin: 15px 0 10px 0;">
    {foreach from=$users_to_suggest item=user}
        {call joinedPerson user=$user no_text=1}
    {/foreach}
</table>
<a href="{$base_url}{$user_url}/connections" style="font: bold 13px 'helvetica neue',helvetica,arial,sans-serif;
                                                padding:0 0 0 0;
                                                margin:0;
                                                text-decoration:none">
    See all your connections.
</a>
<br><br>
<span style="font-size:10px;"><a href="{$base_url}/unsubscribe/suggestion/{$unsubscribe_key}">Unsubscribe</a> or <a href="{$base_url}{$user_url}/account?active_tab=notifications_tab">Manage your email preferences</a></span>

{include file='components/mailer/footer.tpl'}


