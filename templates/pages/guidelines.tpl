{include file='components/header_new.tpl' active_submenu='guidelines'}

<div class="page_sizer_wide rails_container">
    <div class="left_rail">
        <div class="content_container guidelines_container">
            <div class="guidelines_title">ShareBloc Commmunity Guidelines</div>
            <div class="guidelines_descr">We have five simple rules on ShareBloc and we hope you follow them. Thanks for sharing! </div>
            <div class="guidelines_paragraph">1. Don't spam.</div>
            <div> Spam is for <a href="http://en.wikipedia.org/wiki/Spam_musubi">delicious Hawawiian dishes</a>, not for ShareBloc. What do we consider to be spam?</div>
            <div class="guidelines_list">
                a. Poor quality and irrelevant content.<br>
                b. Repeatedly submitting content that is completely self-promotional.<br>
                c. Repeately posting the same content over and over again.
            </div>
            <div class="guidelines_single_text">We will moderate and delete posts and suspend or ban accounts if necessary.</div>
            <div class="guidelines_paragraph">2. Submit only the content you'd want to read.</div>
            <div>Some of you guys write amazing content. If you're written something incredible, you should submit it. We trust your judgment.</div>
            <div class="guidelines_single_text">We also think you probably read a lot of other great content out there. We'd prefer if you submitted other people's content in addition to your own, emphasizing quality over quantity. That means, on occasion, you don't submit one of your own because it's not your best work.</div>
            <div class="guidelines_paragraph">3. Be respectful.</div>
            <div>We'd like to think of ShareBloc as the cocktail party at the best industry conference you've ever attended. This means smart people with similar professional interests having great conversations. If you don't respect your peers, like in real life, you will get thrown out of the party.</div>
            <div class="guidelines_paragraph">4. Please only post professional content.</div>
            <div> There is a time and place for your <a href="http://www.grumpycats.com/">favorite cat videos</a>. This is not one of them. Please post only content you'd expect to read from a respected periodical, like the <a href="http://online.wsj.com/">Wall Street Journal</a> or <a href="http://www.nytimes.com/">New York Times</a>, a relevant industry rag like <a href="http://techcrunch.com/">TechCrunch</a> or from a popular blogger like <a href="http://sethgodin.typepad.com/">Seth Godin</a>.</div>
            <div class="guidelines_paragraph">5. ShareBloc is for humans.</div>
            <div>We love animals, robots and corporations but none of them should have a personal ShareBloc account. If we suspect your account to be a "bot", we will suspend it. If your ShareBloc account is a corporation and not an actual person, we will request you change it to an actual representative of the corporation or suspend it.</div>
            <div class="guidelines_single_text">Thank you for joining our community!</div>
        </div>
    </div>
    <div class="right_rail">
        <div class="right_rail_content">
            {include file='components/front/front_right_post_buttons.tpl'}
            {include file='components/front/invite_link.tpl' type='user'}
        </div>
    </div>
    <div class="clear"></div>
</div>

{include file='components/js_common.tpl'}

<script>
    $(document).ready(function() {
        setRightRailFixed();
        prepareCopyToClipboard();
        prepareSharing();
    });
</script>
{include file='components/footer_new.tpl'}