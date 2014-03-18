{* probably should move this to php *}
{if $type=='share_post'}
    {assign var='twitter_text_truncated' value=$post_data.title|escape|truncate:$twitter_symbols_left:"...":true}
    {if empty($post_data.f_contest)}
        {assign var='input_title' value="Share this post."}
        {assign var='text_to_share' value=$post_data.title|escape|cat:" via @ShareBloc"}
        {assign var='text_to_share_twitter' value=$twitter_text_truncated|cat:" via @ShareBloc"}
        {assign var='text_to_share_email' value="Hi, I thought you might be interested in this link:"}
        {assign var='url_to_share' value=$post_data.title_url}
    {else}
        {if $contest_id == 2}
            {assign var='input_title' value="Vote for a winner on ShareBloc's Content Marketing Nation Contest"}
            {assign var='text_to_share' value="Please help vote for "|cat:$post_data.title|escape|cat:" on @ShareBloc #cntmktgnation14"}
            {assign var='text_to_share_twitter' value="Please help vote for "|cat:$twitter_text_truncated|cat:" on @ShareBloc #cntmktgnation14"}
            {assign var='text_to_share_email' value=$text_to_share}
            {assign var='url_to_share' value=$post_data.iframe_url}
        {else}
            {assign var='input_title' value="Congratulate the winners."}
            {assign var='text_to_share' value="Congratulations to "|cat:$post_data.title|escape|cat:" for being a #2013Top50ContentMarketing winner via @ShareBloc"}
            {assign var='text_to_share_twitter' value="Congratulations to "|cat:$twitter_text_truncated|cat:" for being a #2013Top50ContentMarketing winner via @ShareBloc"}
            {assign var='text_to_share_email' value=$text_to_share}
            {assign var='url_to_share' value=$post_data.my_url_share}
        {/if}
    {/if}
{elseif $type=='contest'}
    {if $contest_id == 1}
        {assign var='input_title' value="Congratulate the winners."}
        {assign var='url_to_share' value='/'|cat:{$contest_url}}
        {assign var='text_to_share' value="Congratulations to the #2013Top50ContentMarketing winners via @ShareBloc."}
        {assign var='text_to_share_twitter' value=$text_to_share}
        {assign var='text_to_share_email' value=$text_to_share}
    {else}
        {assign var='input_title' value="Vote for a winner on ShareBloc's Content Marketing Nation Contest"}
        {assign var='url_to_share' value='/'|cat:{$contest_url}}
        {assign var='text_to_share' value="Come vote on the Content Marketing Nation Contest #cntmktgnation14 on @ShareBloc"}
        {assign var='text_to_share_twitter' value=$text_to_share}
        {assign var='text_to_share_email' value="Friend,\nHelp me vote for a winner on the Content Marketing Nation Contest #cntmktgnation14 on @ShareBloc"}
    {/if}
{elseif $type=='event'}
    {assign var='input_title' value="Share this page"}
    {assign var='url_to_share' value='/calendar/'|cat:{$page_tag.code_name}}
    {assign var='text_to_share' value="Hi, I thought you might be interested in this link:"}
    {assign var='text_to_share_twitter' value=$text_to_share}
    {assign var='text_to_share_email' value=$text_to_share}
{elseif $type=='user' && $logged_in}
    {assign var='input_title' value="Invite others using your own special invite code."}
    {assign var='url_to_share' value="/invite/`$user_info.code_name`"}
    {assign var='text_to_share' value="Join me and thousands of others in @ShareBloc, a community for professionals to share business content that matters."}
    {assign var='text_to_share_twitter' value=$text_to_share}
    {assign var='text_to_share_email' value=$text_to_share}
{/if}

{* We can't show user type for unregistered *}
{if $logged_in || $type!=='user'}
    <div class="front_custom_invite_div {if $type=='share_post'}no_margin_top{elseif $type=='contest'}contest_right_rail_margin{/if}">
        <div>{$input_title}</div>
        <div class="front_custom_invite_input_div">
            <input id="share_url" class="join_input front_custom_invite_input" type="text" value="{$base_url}{$url_to_share}">
        </div>
        <div class="share_post_buttons" id="share_data_div" data-shareUrl="{$base_url}{$url_to_share}">
            <div class="share_btns_line_div">
                <div id="copy_btn" data-clipboard-target="share_url" class="share_post_btn copy_btn">Copy</div>
                <div id="tweet_btn" data-provider="twitter" data-text="{$text_to_share_twitter}" class="share_post_btn" data-shareUrl="{$base_url}{$url_to_share}"><img class="tweet_img" src="/images/twitter.png">Tweet</div>
            </div>
            <div>
                <div id="mail_btn" data-provider="mail" data-text="{$text_to_share_email}" class="share_post_btn" data-shareUrl="{$base_url}{$url_to_share}"><img src="/images/mail.png"></div>
                <div id="fb_share_btn" data-provider="facebook" data-text="{$text_to_share}" class="share_post_btn fb_btn" data-shareUrl="{$base_url}{$url_to_share}"><img class="fb_img" src="/images/facebook_share.png"></div>
{* We can't provide text using this way without og usage wich is a bit problematic *}
{*                <div class="share_post_btn fb_btn">
                    <div class="fb-share-button" data-summary="bear" data-href="{$base_url}{$url_to_share}" data-width="100" data-height="" data-colorscheme="light" data-type="button_count"></div>
                    <div id="fb-root"></div>
                    <script>(function(d, s, id) {
                      var js, fjs = d.getElementsByTagName(s)[0];
                      if (d.getElementById(id)) return;
                      js = d.createElement(s); js.id = id;
                      js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
                      fjs.parentNode.insertBefore(js, fjs);
                    }(document, 'script', 'facebook-jssdk'));</script>
                </div>*}
                <div id="li_share_btn" data-provider="linkedin" data-text="{$text_to_share}" class="share_post_btn" data-shareUrl="{$base_url}{$url_to_share}"><img src="/images/linkedinshare.png"></div>
                <div id="gplus_btn" data-provider="google" data-text="{$text_to_share}" class="share_post_btn" data-shareUrl="{$base_url}{$url_to_share}"><img src="/images/gplus.png"></div>
            </div>
        </div>
    </div>
{/if}
{include file='components/front/share_link_popup.tpl'}
{if !empty($tweet_after_post)}
    {include file='components/front/tweet_type_selection_popup.tpl'}
{/if}