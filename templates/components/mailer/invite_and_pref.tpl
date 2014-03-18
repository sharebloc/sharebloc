{if $email_for_user}
    <br><br>
    We appreciate your support for ShareBloc. Pleace invite other friends to ShareBloc using this invite code: <br>
    <a href="{$base_url}/invite/{if empty($addressee_code_name)}{$user_code_name}{else}{$addressee_code_name}{/if}">
        <b>{$base_url}/invite/{if empty($addressee_code_name)}{$user_code_name}{else}{$addressee_code_name}{/if}</b>
    </a>
    <br><br>
    <span style="font-size:10px;">
        <a href="{$base_url}{if empty($addressee_user_url)}{$user_url}{else}{$addressee_user_url}{/if}/account?active_tab=notifications_tab">
            Manage your email preferences
        </a>
    </span>
{/if}