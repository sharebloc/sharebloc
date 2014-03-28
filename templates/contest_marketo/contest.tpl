{include file='components/header_new.tpl' active='contest'}

{* big image div *}
<div class="contest_image_block">
    <div class="contest_image_btn">
        Take me to the contest!
    </div>
</div>

<div class="summit_block">
    <div>
        Post your favorite content piece about marketing automation. <br>If your post gets the most votes at the end of the contest, you'll win a free ticket to
    </div>
    <a href="#">
        <a href="http://summit.marketo.com/2014/" target="_blank" class="trackedOutboundLink"><img class="marketo_summit_logo" src="images/Summit-logo-2014.png"></a>
    </a>
    <div class="contest_rules">
        <a id="show_contest_rules_link" href="">See contest rules for more information</a>
    </div>

    <div>
        The contest ends on March 28 so get your votes in. Vote up to three times a day.
    </div>
</div>
<div class="sponsors_div">
    <div class="sponsor_links_block">
        <div class="text">With participation from representatives at:</div>
    <div class="main_sponsor_block">
        <a href="http://www.leadmd.com/" target="_blank" class="trackedOutboundLink"><img src="images/leadmd_reg.jpg"></a>
    </div>
    <div class="other_sponsors_block">
        <a href="http://www.ringlead.com/" target="_blank" class="trackedOutboundLink"><img class="secondary_sponsor_image" src="images/ringlead.jpg"></a>
        <a href="https://www.infer.com/" target="_blank" class="trackedOutboundLink"><img class="secondary_sponsor_image" src="images/infer.jpeg"></a>
    </div>
    <div class="other_sponsors_block">
        <a href="http://www.cloudwords.com/" target="_blank" class="trackedOutboundLink"><img class="secondary_sponsor_image" src="images/cloudwords.jpeg"></a>
        <a href="http://www.heinzmarketing.com/" target="_blank" class="trackedOutboundLink"><img class="secondary_sponsor_image" src="images/heinz.jpeg"></a>
    </div>
    </div>
</div>

{include file='contest_marketo/votes_counter_block.tpl'}

<div class="page_sizer_wide rails_container">
    <div class="left_rail">
        {include file='components/front/front_filters_block.tpl' filters_url='/'|cat:$contest_url}
        <div class="content_container">
            <div class="more_container"
                 data-offsetForMore="{$posts_on_contest_page}"
                 data-noMore="{$no_more_content}"
                 data-pageType="contest"
                 data-entityID="0">
                {if $content}
                    {foreach from=$content item=front_post}
                        {include file='components/front/front_post.tpl' post=$front_post}
                    {/foreach}
                {else}
                    No posts yet
                {/if}
            </div>
            <div class="front_loader_div hide"><img src="/images/loading.gif"></div>
        </div>
    </div>
    <div class="right_rail">
        <div class="right_rail_content f_contest">

            {include file='components/front/invite_link.tpl' type='contest'}
            <div class="front_custom_invite_div">
                <div class="contest_sponsors_banner">
                    <div class="sponsors_header">Our sponsors</div>
                    <div class="main_sponsor_block">
                        <a href="http://www.leadmd.com/" target="_blank" class="trackedOutboundLink"><img src="images/leadmdside.jpg"></a>
                    </div>
                    <div class="other_sponsors_block">
                        <a href="http://www.ringlead.com/" target="_blank" class="trackedOutboundLink"><img class="secondary_sponsor_image" src="images/ringleadside.jpg"></a>
                        <a href="https://www.infer.com/" target="_blank" class="trackedOutboundLink"><img class="secondary_sponsor_image" src="images/inferside.png"></a>
                    </div>
                    <div class="other_sponsors_block">
                        <a href="http://www.cloudwords.com/" target="_blank" class="trackedOutboundLink"><img class="secondary_sponsor_image" src="images/cloudwordsside.png"></a>
                        <a href="http://www.heinzmarketing.com/" target="_blank" class="trackedOutboundLink"><img class="secondary_sponsor_image" src="images/heinzside.png"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
{include file='components/js_common.tpl'}
<script>
    var contest_id = {$contest_id};
    var block_bottom = 0;
    var block_fixed = false;
    var should_scroll_to_contest = 1;
    var scroll_to_post = {$scroll_to_post};

    $(document).ready(function() {
        setRightRailFixed(100);
        preparePageForMore();
        prepareContentFilters();
        prepareContentDelete();
        prepareCopyToClipboard();
        prepareSharing();
        prepareVoting();
        prepareContestVoting();

        $(".contest_image_btn").click(function() {
            scrollToContest();
            return false;
        });

        var sponsor_block = $('.sponsors_div');
        block_bottom = sponsor_block.offset().top + sponsor_block.height();
        $(window).scroll(function() {
            fixVotesLeftBlockIfNeeded($(this).scrollTop());
        });

        if (scroll_to_post) {
            scrollToPost();
        } else if (should_scroll_to_contest) {
            scrollToContest();
        }

        $("#nominate_link_buttons_div .cancel_btn").click(function() {
            $("#popup_nominate_link_container").hide();
            $("#nominate_link_form").trigger('reset');
            return false;
        });
    });

    function scrollToPost() {
        var postOffset = $("#posted_link_" + scroll_to_post).offset().top;
        $("html,body").animate({
                                "scrollTop": postOffset-150
                               }, "slow");
    }

    function scrollToContest() {
        $("html,body").animate({
            "scrollTop": block_bottom
        }, "slow");
    }

    function fixVotesLeftBlockIfNeeded(scrollTop) {
        if (scrollTop > block_bottom) {
            if (!block_fixed) {
                $("body").append($('.contest_votes_block').clone(true).addClass('at_top'));
                block_fixed = true;
                if (!should_scroll_to_contest) {
                    setSBCookie('should_scroll_to_contest', 1);
                }
            }
        } else {
            if (block_fixed) {
                $('.contest_votes_block.at_top').remove();
                block_fixed = false;
            }
        }

    }

    function processErrors(errors) {
        if (errors) {
            $("#app_error_div").show();
        }

        $(".join_field_status_img").attr('src', '/images/input_ok.png').attr('title', '').show();

        for (var key in errors) {
            var name = errors[key]['name'];
            $("#contest_" + name).find(".join_field_status_img")
                    .attr('src', '/images/input_error.png')
                    .attr('title', errors[key]['msg']);
        }

    }
</script>

{include file='components/footer_new.tpl'}
