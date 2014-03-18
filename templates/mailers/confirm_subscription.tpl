{include file='components/mailer/header.tpl' title="{$subject}"}

Click here to confirm your SB subscription on {$tag_type_name} emails:
<a href="{$base_url}/confirm/subscribe/{$confirm_email_key}">{$base_url}/confirm/subscribe/{$confirm_email_key}</a>

<br><br>
Cheers,
<br>
David Cheng & Andrew Koller
<br>
Co-founders of ShareBloc
<br />
<a href="mailto:support@sharebloc.com">support@sharebloc.com</a>
{include file='components/mailer/footer.tpl'}