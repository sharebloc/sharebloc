{include file='components/mailer/feed_post_function.tpl'}
{include file='components/mailer/joined_person_function.tpl'}

{***  END OF FUNCTIONS ****}

{include file='components/mailer/header.tpl' title="{$subject}"}
<br>
{$addressee_first_name},<br>
Here are last week's top posts from your feed:

{***** POSTS *****}
<table style="margin: 20px 0 10px 0;
              border-collapse: collapse;
              width: 580px;">
    {foreach from=$posts item=post}
        {call feedPost post=$post no_views=true}
    {/foreach}

   <tr>
       <td colspan="2" style="border-top: 1px solid #cacaca;
                border-bottom: 1px solid #cacaca;
                padding: 10px 15px 10px 0;">
            <a href="{$base_url}?from_weekly=1" style="font:bold 12px 'helvetica neue',helvetica,arial,sans-serif;
                                            text-decoration:none;
                                            color:#005598 ">
                Click here to see your entire feed
            </a>
        </td>
    </tr>
</table>
{***** END OF POSTS *****}
{assign var='invite_code_url' value=$base_url|cat:"/invite/"|cat:$user_code_name}
{assign var='twitter_data' value="Join me and thousands of others in @ShareBloc, a community for professionals to share business content that matters. "|cat:{$invite_code_url}|escape:'url'}

Do you want to improve your feed? Click <a href="{$base_url}/blocs/">here</a> to manage your feed's preferences.
<br><br>
<b>Do you like this email? <a href="http://twitter.com/home/?status={$twitter_data}" target="_blank">Tweet it out</a> or forward it to your friends and they can join with your own invite code: <br>
    <a href="{$invite_code_url}">{$invite_code_url}</a></b>
<br><br>


{***** USERS *****}
{if $combined_people}
    <br>
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
{/if}
{***** END OF USERS *****}

<br><br>
We appreciate your support for ShareBloc.
<br><br>
Cheers,<br>
<a href='http://www.sharebloc.com/users/david_cheng'>David Cheng</a> & <a href='http://www.sharebloc.com/users/andrew_koller'>Andrew Koller</a>
<br>
Co-founders of <a href='http://www.sharebloc.com/'>ShareBloc</a>
<br><br>
<span style="font-size:10px;"><a href="{$base_url}/unsubscribe/weekly/{$unsubscribe_key}">Unsubscribe</a> or <a href="{$base_url}{$user_url}/account?active_tab=notifications_tab">Manage your email preferences</a></span>

{include file='components/mailer/footer.tpl'}


