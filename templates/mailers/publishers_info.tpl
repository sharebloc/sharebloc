{function name=weeklyPost post=array()}
    <tr>
        <td style="border-top: 1px solid #cacaca;
                    border-bottom: 1px solid #cacaca;
                    padding: 10px 15px 10px 0;
                    vertical-align: top;">
            {if $post.logo_url_thumb}
                <img src="{$base_url}{$post.logo_url_thumb}" style="width:58px;
                                                                    height:58px;"/>
            {/if}
        </td>
        <td style="border-top: 1px solid #cacaca;
                border-bottom: 1px solid #cacaca;
                padding: 10px 0 10px 0;
                vertical-align: top;
                line-height: 1em;
                vertical-align: middle;
                ">
            <h1 style="margin:0">
                <a href="{$post.my_url}"
                   style="font:bold 15px 'helvetica neue', helvetica, arial, sans-serif;
                            text-decoration:none;">
                    {$post.title|escape}
                </a>
                <br>
                {assign var='twitter_data' value=$post.title|escape|truncate:$twitter_symbols_left:"...":true|cat:" via @ShareBloc "|cat:$base_url|cat:$post.title_url|escape:'url'}
                <a href="http://twitter.com/home/?status={$twitter_data}" target="_blank"
                   style="font:bold 15px 'helvetica neue', helvetica, arial, sans-serif;
                            text-decoration:none;">Tweet</a>
            </h1>
        </td>
    </tr>
{/function}

{***  END OF FUNCTIONS ****}

{include file='components/mailer/header.tpl' title="{$subject}"}
<br>
{$addressee_first_name},<br>
Thanks for being a ShareBloc publisher. Here are the links that were posted on your behalf today:

{***** POSTS *****}
<table style="margin: 20px 0 10px 0;
              border-collapse: collapse;
              width: 580px;">
    {foreach from=$posts item=post}
        {weeklyPost post=$post}
    {/foreach}
</table>
{***** END OF POSTS *****}

<br><br>
To help your posts reach the top of everyone's feed, be sure to share and tweet those links and ask your network to vote.
<br><br>
Regards,<br>
<a href='http://www.sharebloc.com/'>ShareBloc</a>
<br><br>
This is an automated email that is part of the ShareBloc Publisher Program.
<br><br>

{include file='components/mailer/footer.tpl'}


