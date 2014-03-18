{include file='components/mailer/header.tpl' title='Your password has been changed'}

Hello {$addressee_first_name}, password has been changed. If you didn't do this, please secure your account or <a href="mailto:support@sharebloc.com">contact us</a>.
{include file='components/mailer/invite_and_pref.tpl'}
<br /><br />
ShareBloc Team
<br />
<a href="mailto:support@sharebloc.com">support@sharebloc.com</a>
{include file='components/mailer/footer.tpl'}