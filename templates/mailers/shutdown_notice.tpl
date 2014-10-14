{include file='components/mailer/header.tpl' title="{$subject}"}

{$addressee_first_name},<br><br>

We are reaching out to you to let you know that we have decided to shut down ShareBloc as of 10/19. We want to thank you so much for coming on this journey with you.
<br>
We've <a href="http://blog.sharebloc.com/post/99882229811/sharebloc-is-shutting-down">posted to our blog</a> information about the shutdown. 
<br>
If you have any questions or concerns about the shutdown, please don't hesitate to reach out to us at support@sharebloc.com.
<br>
Thank you for all your support.

<br><br>
Cheers,<br>
<a href='{$base_url}/users/david_cheng'>David Cheng</a> & <a href='{$base_url}/users/andrew_koller'>Andrew Koller</a>
<br>
Co-founders of <a href='{$base_url}'>ShareBloc</a>
<br><br>

<br><br>
<span style="font-size:10px;"><a href="{$base_url}/unsubscribe/updates/{$unsubscribe_key}" target="_blank">Unsubscribe</a> or <a href="{$base_url}{$user_url}/account?active_tab=notifications_tab" target="_blank">Manage your email preferences</a></span>

{include file='components/mailer/footer.tpl'}