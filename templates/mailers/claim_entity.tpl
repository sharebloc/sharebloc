{include file='components/mailer/header.tpl' title='Your ShareBloc Profile Claim'}

Thank you for claiming the profile for <a href="http://%%HTTP_HOST%%%%CLAIMED_ENTITY_URL%%">%%CLAIMED_ENTITY_NAME%%</a>.
<br /><br />
To confirm your claim on this profile, please click this link: <br>
<a href="http://%%HTTP_HOST%%/companies/claimkey%%CLAIM_KEY%%">http://%%HTTP_HOST%%/companies/claimkey%%CLAIM_KEY%%</a>
<br /><br />
Thanks and we look forward to sending high-quality leads to you and your company. If you have any questions, please email us at <a href="mailto:help@sharebloc.com">help@sharebloc.com</a>.
<br /><br />
- The ShareBloc Team
<br />

{include file='components/mailer/footer.tpl'}