{include file='components/mailer/header.tpl' title="{$sender_full_name} invites you to try out ShareBloc, a website for business content sharing."}

{$invite_front_text}
<br /><br />
<a href="{$base_url}/invite/{$confirm_key}">Click here to check it out</a>
<br /><br />
ShareBloc Team
<br />
<a href="mailto:support@sharebloc.com">support@sharebloc.com</a>
{include file='components/mailer/footer.tpl'}