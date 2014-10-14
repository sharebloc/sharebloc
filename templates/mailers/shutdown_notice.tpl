{include file='components/mailer/header.tpl' title="{$subject}"}

{$addressee_first_name},<br><br>

We are reaching out to let you know that we have decided to shut down ShareBloc as of 10/19. We're so grateful for the community that diligently posted, voted and shared awesome content. Detailed information about the shutdown can be found <a href="http://blog.sharebloc.com/post/99882229811/sharebloc-is-shutting-down">posted to our blog</a>.
<br><br>
If you have any questions or concerns, please contact support@sharebloc.com.
<br><br>
Thank you so much for coming on this journey with us.
<br><br>
Cheers,<br>
<a href='{$base_url}/users/david_cheng'>David Cheng</a> & <a href='{$base_url}/users/andrew_koller'>Andrew Koller</a>
<br>
Co-founders of <a href='{$base_url}'>ShareBloc</a>
<br><br>

<br><br>
<span style="font-size:10px;"><a href="{$base_url}/unsubscribe/updates/{$unsubscribe_key}" target="_blank">Unsubscribe</a> or <a href="{$base_url}{$user_url}/account?active_tab=notifications_tab" target="_blank">Manage your email preferences</a></span>

{include file='components/mailer/footer.tpl'}