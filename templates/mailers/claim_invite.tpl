{include file='components/mailer/header.tpl' title='Your ShareBloc Profile Claim'}
Hello %%FIRST_NAME%%, <br>
You can now claim the <a href="http://%%HTTP_HOST%%%%CLAIMED_ENTITY_URL%%">%%CLAIMED_ENTITY_NAME%%</a> profile on ShareBloc.
<br /><br />
To claim this profile, click the link below (or copy and paste the URL into your browser):<br>
<a href="http://%%HTTP_HOST%%/companies/claimkey%%CLAIM_KEY%%">http://%%HTTP_HOST%%/companies/claimkey%%CLAIM_KEY%%</a>
<br /><br />
By claiming the profile, you can manage the UserVoice profile's information and invite others to write reviews on UserVoice.
<br /><br />
If this does not pertain to you, please disregard this email.
<br />
ShareBloc Team
<br />
<a href="mailto:support@sharebloc.com">support@sharebloc.com</a>

{include file='components/mailer/footer.tpl'}