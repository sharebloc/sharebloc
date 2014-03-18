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
                ">
            <h1 style="margin:0">
                <a href="{$base_url}{$post.title_url}"
                   style="font:bold 15px 'helvetica neue', helvetica, arial, sans-serif;
                            text-decoration:none;">
                    {$post.title|escape}
                </a>
            </h1>
            <h2 style="font:bold 12px 'helvetica neue',helvetica,arial,sans-serif;
                padding:0;
                margin:0">
                Posted by
                {if $post.user.my_url}
                    <a href="{$base_url}{$post.user.my_url}{$ref_suffix}" style="text-decoration:none">{$post.user.full_name|escape}</a>
                {else}
                    {$post.user.full_name|escape}
                {/if}
                {if $post.categories}
                    to <a href="{$base_url}{$post.categories[0].my_url}{$ref_suffix}" style="text-decoration:none">{$post.categories[0].tag_name}</a>
                {/if}
                on {$post.date} - <a href="{$base_url}{$post.my_url}{$ref_suffix}" style="text-decoration:none">{$post.views_count} views, {$post.vote.total} Point{if $post.vote.total!=1}s{/if}, {$post.comment_count} Comment{if $post.comment_count!=1}s{/if}</a>
            </h2>
            {if !empty($post.url)}
                <h3 style="font:normal 13px 'helvetica neue',helvetica,arial,sans-serif;
                            padding:2px 2px 0 0;
                            margin:3px 0 0 0">
                    {$post.text|strip_tags|truncate:200:" (...)"|nl2br}
                    <a href="{$post.url}" style="font-size:11px;
                                                        text-decoration: none;
                                                        color:#005598">
                        Visit Link
                    </a>
                </h3>
            {/if}
        </td>
    </tr>
{/function}

{***  END OF FUNCTIONS ****}

{assign var='ref_suffix' value='?ref=bloc_feed-'|cat:$vendor_category_list.$tag_id.code_name}
{include file='components/mailer/header.tpl' title="{$subject}"}
Hello!<br>
<br>
Here are last week's top posts for {$vendor_category_list.$tag_id.tag_name}:

{***** POSTS *****}
<table style="margin: 20px 0 10px 0;
              border-collapse: collapse;
              width: 580px;">
    {foreach from=$posts item=post}
        {weeklyPost post=$post}
    {/foreach}

   <tr>
       <td colspan="2" style="border-top: 1px solid #cacaca;
                border-bottom: 1px solid #cacaca;
                padding: 10px 15px 10px 0;">
            <a href="{$base_url}/{$vendor_category_list.$tag_id.my_url}{$ref_suffix}" style="font:bold 12px 'helvetica neue',helvetica,arial,sans-serif;
                                            text-decoration:none;
                                            color:#005598 ">
                Click here to see all posts for {$vendor_category_list.$tag_id.tag_name}
            </a>
        </td>
    </tr>
</table>
{***** END OF POSTS *****}

<div style="width: 580px;">
    <span style="font-weight: bold;">Do you like this email? Join ShareBloc and receive an even more curated email based on your professional interests:</span>
    <br>
    <a href="{$base_url}/join{$ref_suffix}" style="font:bold 12px 'helvetica neue',helvetica,arial,sans-serif;
                                    text-decoration:none;
                                    color:#005598 ">{$base_url}/join{$ref_suffix}</a>
    <br><br>
    <span><a style="font-size:11px;color:#005598;text-decoration:none;" href="{$base_url}/unsubscribe/subscription/{$unsubscribe_key}">Unsubscribe</a></span>
</div>
{include file='components/mailer/footer.tpl'}


