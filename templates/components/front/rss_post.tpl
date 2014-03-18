{if $post.logo_url_full}
    <div style="float:left; margin-right: 15px;"><img src="{$base_url}{$post.logo_url_thumb}"></div>
{/if}
{if $post.outer_link_host}
    <a href="{$post.outer_link_host}" target="_blank">({$post.outer_link_host})</a>
{/if}
<br><br>
{if $post.text}
    {$post.text|nl2br}
{/if}
<br><br>
Posted
{if empty($no_post_author)}by
    <span>
        {if $post.user.my_url}
            <a href="{$base_url}{$post.user.my_url}" target="_blank">{$post.user.full_name|escape}</a>
        {else}
            {$post.user.full_name|escape}
        {/if}
    </span>
{/if}
<br><br>
{if $post.comment_count > 0}
    <a href="{$base_url}{$post.my_url}" target="_blank">{$post.comment_count} {$post.comments_title}</a>
{else}
    <a href="{$base_url}{$post.my_url}" target="_blank">Add a comment</a>
{/if}