{include file='components/mailer/header.tpl' title="{$sender_full_name} wants to invite you to try out ShareBloc"}

{$invite_front_text}
<br /><br />
<a href="{$base_url}/join/{$confirm_key}">Click here to check it out</a>
<br /><br />
ShareBloc Team
<br />
<a href="mailto:support@sharebloc.com">support@sharebloc.com</a>
{include file='components/mailer/footer.tpl'}