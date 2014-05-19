{include file='components/mailer/header.tpl' title="{$subject}"}

{$addressee_first_name},<br><br>


Thank you for being a great ShareBloc user. We started ShareBloc to provide a curation platform for all the content we consume for work. Unfortunately, we've bit off more than we can chew. Starting in June, we'll be focusing our efforts on only the <a href="http://www.sharebloc.com/blocs/sales__marketing">sales & marketing</a> community. This means we'll be shutting down the community blocs for all other cateogories, like technology and real estate.
<br><br>

We realize that many of you aren't interested in sales & marketing. If you'd like to disable your account, <a href="{$base_url}/unsubscribe/deactivate/{$unsubscribe_key}">click this link</a> and we'll remove your account info within 48 hours. Thanks again for coming with us on this journey.

<br><br>
Cheers,<br>
<a href='{$base_url}/users/david_cheng'>David Cheng</a> & <a href='{$base_url}/users/andrew_koller'>Andrew Koller</a>
<br>
Co-founders of <a href='{$base_url}'>ShareBloc</a>
<br><br>

<br><br>
<span style="font-size:10px;"><a href="{$base_url}/unsubscribe/deactivate/{$unsubscribe_key}" target="_blank">Deactivate your account</a> or <a href="{$base_url}{$user_url}/account?active_tab=notifications_tab" target="_blank">Manage your email preferences</a></span>

{include file='components/mailer/footer.tpl'}