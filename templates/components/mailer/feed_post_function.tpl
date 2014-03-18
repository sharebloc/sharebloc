{function name=feedPost post=array()}
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
                    <a href="{$base_url}{$post.user.my_url}" style="text-decoration:none">{$post.user.full_name|escape}</a>
                {else}
                    {$post.user.full_name|escape}
                {/if}
                {if $post.categories}
                    to <a href="{$base_url}{$post.categories[0].my_url}" style="text-decoration:none">{$post.categories[0].tag_name}</a>
                {/if}
                on {$post.date} - <a href="{$base_url}{$post.my_url}" style="text-decoration:none">{if !isset($no_views)}{$post.views_count} views, {/if}{$post.vote.total} Point{if $post.vote.total!=1}s{/if}, {$post.comment_count} Comment{if $post.comment_count!=1}s{/if}</a>
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