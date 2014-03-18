{include file='components/mailer/header.tpl' title='Your ShareBloc Password Recovery'}

Hello {$addressee_first_name},
<br /><br />
ShareBloc received a request to reset your password.
<br /><br />
To reset your password,  click on the link below (or copy and paste the URL into your browser):<br>
<a href="{$password_reset_link}">{$password_reset_link}</a>
<br /><br />
If you did not make this request, please disregard this e-mail.
<br /><br />
ShareBloc team
<br />
<a href="mailto:support@sharebloc.com">support@sharebloc.com</a>

{include file='components/mailer/footer.tpl'}