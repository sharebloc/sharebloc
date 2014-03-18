{include file='components/header_new.tpl' active='contest'}

{* big image div *}
<div class="contest_image_block">
    <div class="contest_image_text">
        Top 50
        <br/>
        Content Marketing Posts
        <br/>
        of 2013
    </div>
    <div class="contest_image_btn">
        Take me to the contest!
    </div>
</div>

<div class="contest_experts_block">
    <div class="experts_block_content">
        <div class="contest_experts_text">
            We asked some of the leading Sales & Marketing experts <br>to nominate the best content they've read this year.
        </div>
        <div class="experts_div">
            {foreach from=$experts item=expert}
                <div class="expert_div">
                    <img class="expert_img" src='{$base_url}/{$expert.logo_url|default:'images/anonymous_user.png'}' alt='{$expert.full_name}'>
                    <div class="expert_name">{$expert.full_name}</div>
                    <div class="expert_position"><a href="{$expert.position_link}">{$expert.position}</a></div>
                    <div class="user_links">
                        {if $expert.twitter}<a href="{$expert.twitter}" class="trackedOutboundLink"><img src="/images/icons/twitter_white.png"></a>{/if}
                        {if $expert.linkedin}<a href="{$expert.linkedin}" class="trackedOutboundLink"><img src="/images/icons/linkedin_white.png"></a>{/if}
                        {if $expert.google_plus}<a href="{$expert.google_plus}" class="trackedOutboundLink"><img src="/images/icons/googleplus_white.png"></a>{/if}
                        {if $expert.my_url}<a href="{$expert.my_url}"><img src="/images/icons/sharebloc_white.png"></a>{/if}
                        <div class="clear"></div>
                    </div>
                </div>
            {/foreach}
        </div>
        <div class="contest_experts_text experts_text1">Now we want to hear from you.</div>
        <div class="contest_experts_text">Let us know what was the best content marketing you've read this year.<br>Or if you don't see anything you like, nominate a piece yourself!</div>
    </div>
</div>

{include file='components/contest/votes_counter_block.tpl'}

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
                        {include file='components/contest/contest_post.tpl' post=$front_post}
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
            <!--a class="contest_nominate_link" href="/post/contest">Nominate a content piece</a-->
            {include file='components/front/invite_link.tpl' type='contest'}
            <div class="front_custom_invite_div contest_right_rail_margin">
                <div class="front_post_site_map">
                    <a href="/{$contest_url}/all"><img class="all_nominations_img" src="images/nomination_list.png"/></a>
                    <a href="/{$contest_url}/all">See all the nominations.</a>
                </div>
            </div>
            <div class="front_custom_invite_div contest_right_rail_margin">
                <a href="/blocs/sales__marketing">
                    <img class="sales_marketing_img" src="images/contest/sales_marketing.png"/>
                    <div class="vote_best_sales_first_line">
                        Want to keep voting on the best sales & marketing content?
                    </div>
                    <div>
                        Vote every day here.
                    </div>
                    <div class="clear"></div>
                </a>
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
    var should_scroll_to_contest = getSBCookie('should_scroll_to_contest');
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

        $("#popup_submit_votes_container .cancel_btn").click(function() {
            $("#popup_submit_votes_container").hide();
            return false;
        });

        var experts_bloc = $('.contest_experts_block');
        block_bottom = experts_bloc.offset().top + experts_bloc.height();
        $(window).scroll(function() {
            fixVotesLeftBlockIfNeeded($(this).scrollTop());
        });

        if (scroll_to_post) {
            scrollToPost();
        } else if (should_scroll_to_contest) {
            scrollToContest();
        }
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