{include file='components/header_new.tpl'}

<div class="page_sizer">
    {include file='components/join_steps.tpl'}
    <div class="content_container join_step_container">
        <div class="join_container_block page_title">
            <span class="page_title_text">
                {if $join_follow_type=='bloc'}
                    Follow a Community Bloc
                {elseif $join_follow_type=='company'}
                    Follow Some Companies
                {elseif $join_follow_type=='people' || $join_follow_type=='networks'}
                    Follow Your Network
                {/if}
            </span>
        </div>
        <div class="join_container_block page_title">
            <span class="join_subtitle">
                {if $join_follow_type=='bloc'}
                    Follow one or more community blocs so you can get the content that is most relevant to you!
                {elseif $join_follow_type=='company'}
                    <div class="search_company_title">Follow three or more companies that you find interesting</div>
                    <input type="text" class="join_input" id="search_company" name="search_company" placeholder="Search for a Company" value="">
                {elseif $join_follow_type=='people'}
                    {if $only_default_follow_users}
                        It looks like no one in your network has joined ShareBloc yet. Don't worry, you'll have a chance to invite others next.<br>
                     {else}
                        We found some people you may know from your social networks. Follow some or all of them.<br>
                     {/if}
                     We suggest you follow David & Andrew (the founders!). We post great stuff.
                {elseif $join_follow_type=='networks'}
                    You haven't connected to a social network yet. Connect one now to follow your network.
                {/if}
            </span>
        </div>
        <div class="join_container_block {if $join_follow_type!='networks'}join_follow_section{else}networks_follow_section{/if}">
            {if $join_follow_type=='people'}
                <div class="follow_all_div">
                    <input class="follow_all_chk" type="checkbox" data-whomType='user' checked>Follow/Unfollow All
                </div>
            {elseif $join_follow_type=='company' && $follows}
                <div class="recommended_companies_title">Here are 20 companies we recommend for you</div>
            {/if}
            <form id="contacts_form" method="POST" autocomplete="off">
                {if $join_follow_type=='networks'}
                    <div class="social_btns_div">
                        <a data-displayName="LinkedIn" data-provider="linkedin" class='sign_social_btn {if !empty($user_info.oauth.linkedin)}active_btn{/if}' href="{if empty($user_info.oauth.linkedin)}/ext_auth.php?provider=linkedin&type=follow_connect{/if}">
                            <img class="account_social_logo" src="/images/linkedin.png">
                            <span class="sign_social_media_text">{if empty($user_info.oauth.linkedin)}Connect with LinkedIn{else}Connected with LinkedIn{/if}</span>
                        </a>
                        <a data-displayName="Twitter" data-provider="twitter" class='sign_social_btn {if !empty($user_info.oauth.twitter)}active_btn{/if}' href="{if empty($user_info.oauth.twitter)}/ext_auth.php?provider=twitter&type=follow_connect{/if}">
                            <img class="account_social_logo" src="/images/twitter.png">
                            <span class="sign_social_media_text">{if empty($user_info.oauth.twitter)}Connect with Twitter{else}Connected with Twitter{/if}</span>
                        </a>
                        <a data-displayName="Gmail" data-provider="google" class='sign_social_btn {if !empty($user_info.oauth.google)}active_btn{/if}' href="{if empty($user_info.oauth.google)}/ext_auth.php?provider=google&type=follow_connect{/if}">
                            <img class="account_social_logo" src="/images/gmail.png">
                            <span class="sign_social_media_text"> {if empty($user_info.oauth.google)}Connect with Gmail{else}Connected with Gmail{/if}</span>
                        </a>
                        <div class="clear"></div>
                        <div class="coming_soon_text">We will never post anything without your permission.</div>
                    </div>
                {else}
                    <div id="following_div" class="">
                        {if $follows}
                            {foreach from=$follows item=follow}
                                {include file='components/front/front_follows.tpl' target_blank=true}
                            {/foreach}
                        {/if}
                        <div class="clear"></div>
                    </div>
                {/if}
            </form>
        </div>
        <div class="join_container_block fields_section">
            <div id="app_error_div" class="app_error join_invites_app_error">
                Sorry, there is something wrong with your app.
            </div>
            {if $skip_allowed_count===0}
                <a href="#" class="join_invites_skip">Skip</a>
            {/if}
            <a href="#" id="join_follow_submit" class="join_invites_submit">Next</a>
            <div class="clear"></div>
        </div>
    </div>
</div>

{include file='components/js_common.tpl'}
<script>
    var btn_disabled = false;
    var next_follow_type = '{$next_follow_type}';
    var skip_allowed_count = {$skip_allowed_count};
    var autocomplete_companies = {
        source: function(request, response) {
            $.ajax({
                url: "/autocomplete.php",
                dataType: "jsonp",
                deferRequestBy: 300,
                data: {
                    only_companies: 0,
                    featureClass: "user[company_name]",
                    style: "full",
                    maxRows: 10,
                    name_startsWith: request.term
                },
                success: function(data) {
                    response(
                    $.map(data.results, function(item) {
                        return {
                            id: item.ID,
                            label: item.Name,
                        };
                    }));
                }
            });
        },
        minLength: 2,
        select: function(event, data) {
            $(this).val(data.item.label);
            processCompanySelect(data.item.id);
            return false;
        },
        open: function() { },
        close: function() { }
    };

    $(document).ready(function() {
        prepareFollowing(true);

        $("#search_company").autocomplete(autocomplete_companies);
        $("#search_company").blur(function(){
            if (!$(this).val()) {
                $('.follow_block').show();
                correctFollowsBlockMargin();
            }
        });

        $(".join_invites_skip").click(function() {
            showNextPage();
        });

        $("#join_follow_submit").click(function() {
            if (checkIfNextBtnDisabled()) {
                alert('Please follow at least '+skip_allowed_count+' to continue!');
                return false;
            }
            showNextPage();
            return false;
        });

        setNextBtnActivity();
    });
    function checkIfNextBtnDisabled() {
         if (skip_allowed_count && $(".profile_follow.active").length < skip_allowed_count) {
            return true;
        } else {
            return false;
        }
    }
    function setNextBtnActivity() {
        if (checkIfNextBtnDisabled()) {
            $("#join_follow_submit").addClass('inactive');
        } else {
            $("#join_follow_submit").removeClass('inactive');
        }
    }
    function processCompanySelect(vendor_id) {
        $(".profile_follow").each(function(){
            if (!$(this).hasClass('active')) {
                $(this).parents('.follow_block').hide();
            }
        });

        $(".recommended_companies_title").hide();

        var insert_before_block = $(".follow_block:first");

        var existing_company = $(".follow_block[data-followeruid=vendor_"+vendor_id+"]");
        if (existing_company.length) {
            existing_company.detach();
            insert_before_block.before(existing_company);
            existing_company.show();
            correctFollowsBlockMargin();
            return;
        }

        $.ajax({
            data: {
                cmd: 'getFollowBlock',
                entity_type: 'vendor',
                entity_id: vendor_id,
            },
            success: function(data) {
                if (data.status === 'success') {
                    var follow_block = $(data.follow_block_html);
                    insert_before_block.before(follow_block);
                    correctFollowsBlockMargin();
                } else {
                    correctFollowsBlockMargin();
                    alert(data.message);
                }
            }
        });
    }

    function showNextPage() {
        if (next_follow_type) {
            $(location).attr('href', '/join_follow.php?type='+next_follow_type);
        } else {
            $(location).attr('href', '/join_invites.php');
        }
    }
</script>

{include file='components/footer_new.tpl'}