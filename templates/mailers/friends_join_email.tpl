{include file='components/mailer/joined_person_function.tpl'}

{include file='components/mailer/header.tpl' title="{$subject}"}
<br>
{$addressee_first_name},<br>
You have {$combined_people|count} new followers and connections on ShareBloc:

<table style="font-size:13px;
              margin: 15px 0 10px 0;">
    {foreach from=$combined_people item=user name=ppl}
        {* todo should prepare count of users needed in php script *}
        {if $smarty.foreach.ppl.index < 5}
            {call joinedPerson user=$user}
        {/if}
    {/foreach}
</table>
<a href="{$base_url}{$user_url}/connections?tab_selected=recent" style="font: bold 13px 'helvetica neue',helvetica,arial,sans-serif;
                                                padding:0 0 0 0;
                                                margin:0;
                                                text-decoration:none">
    See all {$combined_people|count} connections.
</a>
<br><br>
<span style="font-size:10px;"><a href="{$base_url}/unsubscribe/weekly/{$unsubscribe_key}">Unsubscribe</a> or <a href="{$base_url}{$user_url}/account?active_tab=notifications_tab">Manage your email preferences</a></span>

{include file='components/mailer/footer.tpl'}


