{include file='components/mailer/header.tpl' title='Welcome to ShareBloc!'}

Hello {$addressee_first_name},
<br /><br />
Welcome to ShareBloc! We built ShareBloc to help you discover better business content with other professionals like you.
<br /><br />
{if $generated_password}
    Your password is "{$generated_password}". You can always change it from your account page.
    <br /><br />
{/if}
{if $use_email_confirmation}
    In order to help us serve you better, please <a style="color: #00AEEF; font-weight: bold;" href="{$base_url}{$confirm_link}">confirm your e-mail address</a> by visiting:
    <br /><a style="color: #00AEEF; font-weight: bold;" href="{$base_url}{$confirm_link}">{$base_url}{$confirm_link}</a>
    <br /><br />
{/if}
Thanks for your interest in ShareBloc!
{include file='components/mailer/invite_and_pref.tpl'}
<br /><br />
ShareBloc team
<br />
<a href="mailto:support@sharebloc.com">support@sharebloc.com</a>

{include file='components/mailer/footer.tpl'}