{if $reason=='my_post_commented'}
    {include file='components/mailer/header.tpl' title="{$author_full_name} commented on your post \"{$post_title}\""}
{else}
    {include file='components/mailer/header.tpl' title="{$author_full_name} commented on the post \"{$post_title}\""}
{/if}

{$addressee_first_name},
{if $reason=='my_post_commented'}
{$author_full_name} commented on your {$post_type_name} <a href="{$base_url}{$post_url}">"{$post_title}"</a>.
{else}
{$author_full_name} commented on the {$post_type_name} <a href="{$base_url}{$post_url}">"{$post_title}"</a>.
{/if}
<br>
<i>{$comment_text}</i>
<br><br>
Would you like to <a href="{$base_url}{$post_url}">reply</a>?
{include file='components/mailer/invite_and_pref.tpl'}
{include file='components/mailer/footer.tpl'}