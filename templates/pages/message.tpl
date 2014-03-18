{* Page to show messages/errors to user like 404 or invalid confirmation keys *}
{include file='components/header_new.tpl' hide_search='1'}

<div align="center">
    <div class="system_message centered">
        {$message}
    </div>
    <div>
        <a href="/">Back to the Homepage</a>
    </div>
</div>

{include file='components/js_common.tpl'}
{include file='components/footer_new.tpl'}