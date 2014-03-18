{include file='components/header_new.tpl'}

{if $edit_type=='account'}
<div class="page_sizer_wide">
    <div class="user_page_container">
        <div id="main_account_menu" class="left_account_part">
            <a id="account_tab" class="account_menu_link {if $active_tab == 'account_tab'}active{/if}">Account</a>
            <a id="password_tab" class="account_menu_link {if $active_tab == 'password_tab'}active{/if}">Change Password</a>
            <a id="networks_tab" class="account_menu_link {if $active_tab == 'networks_tab'}active{/if}">Social Networks</a>
            <a id="notifications_tab" class="account_menu_link {if $active_tab == 'notifications_tab'}active{/if}">Manage Emails and Notifications</a>
            {if $autopost_allowed}
                <a id="pubtools_tab" class="account_menu_link {if $active_tab == 'pubtools_tab'}active{/if}">Publisher Tools</a>
            {/if}
        </div>
        <div class="right_account_part">
            <form class="sign_form user_settings_part {if !($active_tab == 'account_tab')}hide{/if}" id="account_part">
                {foreach $account_data key=key item=field}
                    <div id="edit_field_{$key}" class="join_field_row">
                        <div class="join_field_title">{$field.title}</div>
                        <input type="text" class="join_input" id="{$key}" name="{$key}" value="{$field.value}">
                        <div class="join_field_status">
                            <img class="join_field_status_img" src="/images/input_ok.png">
                        </div>
                    </div>
                {/foreach}
                <div id="account_app_error_div" class="app_error">
                    Sorry, check your errors.
                </div>
                <div id="account_success_div" class="app_success">
                    Your profile has been changed.
                </div>
                <a class="sign_btn" id="account_save_button" href="#">Save</a>
            </form>
            <form class="sign_form user_settings_part {if !($active_tab == 'password_tab')}hide{/if}" id="password_part">
                {foreach $password_data key=key item=field}
                    <div id="edit_field_{$key}" class="join_field_row">
                        <div class="join_field_title">{$field.title}</div>
                        <input type="password" class="join_input" id="{$key}" name="{$key}" value="">
                        <div class="join_field_status">
                            <img class="join_field_status_img" src="/images/input_ok.png">
                        </div>
                    </div>
                {/foreach}
                <div id="password_app_error_div" class="app_error">
                    Sorry, check your errors.<!--Sorry, your current password is incorrect.-->
                </div>
                <div id="password_success_div" class="app_success">
                    Your password has been changed.
                </div>
                <a class="sign_btn" id="password_save_button" href="#">Save</a>
            </form>
            <div class="user_settings_part {if !($active_tab == 'networks_tab')}hide{/if}" id="networks_part">
                <div class="social_btn_div">
                    <a data-displayName="LinkedIn" data-provider="linkedin" class='wide_btn sign_social_btn {if !empty($user_info.oauth.linkedin)}active_btn{/if}' href="{if empty($user_info.oauth.linkedin)}/ext_auth.php?provider=linkedin&type=connect{/if}">
                        <img class="account_social_logo" src="/images/linkedin.png">
                        <span class="sign_social_media_text">{if empty($user_info.oauth.linkedin)}Connect with LinkedIn{else}Connected with LinkedIn{/if}</span>
                    </a>
                </div>
                <div class="social_btn_div">
                    <a data-displayName="Twitter" data-provider="twitter" class='wide_btn sign_social_btn {if !empty($user_info.oauth.twitter)}active_btn{/if}' href="{if empty($user_info.oauth.twitter)}/ext_auth.php?provider=twitter&type=connect{/if}">
                        <img class="account_social_logo" src="/images/twitter.png">
                        <span class="sign_social_media_text">{if empty($user_info.oauth.twitter)}Connect with Twitter{else}Connected with Twitter{/if}</span>
                    </a>
                </div>
                <div class="social_btn_div">
                    <a data-displayName="Gmail" data-provider="google" class='wide_btn sign_social_btn {if !empty($user_info.oauth.google)}active_btn{/if}' href="{if empty($user_info.oauth.google)}/ext_auth.php?provider=google&type=connect{/if}">
                        <img class="account_social_logo" src="/images/gmail.png">
                        <span class="sign_social_media_text"> {if empty($user_info.oauth.google)}Connect with Gmail{else}Connected with Gmail{/if}</span>
                    </a>
                </div>
            </div>

            <div class="user_settings_part {if !($active_tab == 'notifications_tab')}hide{/if}" id="notifications_part">
                <div class="user_notifications_title">Manage Emails and Notifications</div>
                <div class="subtabs_btns_div">
                    <a class="switch_subtub post_type_link fleft {if $notifications_div=='email'}active{/if}" data-subtab="notify_emails">Emails</a>
                    <a class="switch_subtub post_type_link fleft {if $notifications_div=='notification'}active{/if}" data-subtab="notify_notifications">Notifications</a>
                    <div class="clear"></div>
                </div>
                <div id="notify_notifications" class="subtab_div {if $notifications_div!='notification'}hide{/if}">
                    <div class="">
                        <a id="notifications_clear_lnk" class="notifications_clear_lnk" href="#">Clear</a>
                    </div>
                    <div class="clear"></div>
                    {foreach $notifications item=notification}
                        <div class="notification fleft notification_line">
                            {foreach $notification.authors_html item=author name=count}
                                <a class="notification_author_link" href="{$author.my_url}" target="_blank">{$author.full_name|escape}</a>{if $smarty.foreach.count.index+1 < $notification.authors_html|count}, {/if}
                            {/foreach}
                            {if $notification.rest_authors_text}
                                and <a class="notification_author_link" href="{$notification.my_url}" target="_blank">{$notification.rest_authors_text}</a>
                            {/if}
                            commented on {if $notification.reason=='my_post_commented'}your{else}the{/if} post
                            <a href="{$notification.my_url}" target="_blank">{$notification.post_title|escape}</a> <span class="notification_date">{$notification.last_comment_date|date_format:"%e %b"}</span>
                        </div>
                    {/foreach}
                </div>
                <div id="notify_emails" class="subtab_div emails_div {if $notifications_div!='email'}hide{/if}">
                    <div class="email_settings_div">
                        <input class='change_email_setting' data-setting='notify_weekly' id="notify_weekly" type="checkbox" {if $notify_weekly}checked{/if}>
                        <label for="notify_weekly" class="notifications_email_label">Send me a weekly email on my feed</label>
                    </div>
                    <div class="email_settings_div">
                        <input class='change_email_setting' data-setting='notify_daily' id="notify_daily" type="checkbox" {if $notify_daily}checked{/if}>
                        <label for="notify_daily" class="notifications_email_label">Send me a daily email on my feed</label>
                    </div>
                    <div class="email_settings_div">
                        <input class='change_email_setting' data-setting='notify_post_responded' id="notify_post_responded" type="checkbox" {if $notify_post_responded}checked{/if}>
                        <label for="notify_post_responded" class="notifications_email_label"> Send me an email when someone responds to my post</label>
                    </div>
                    <div class="email_settings_div">
                        <input class='change_email_setting' data-setting='notify_comment_responded' id="notify_comment_responded" type="checkbox" {if $notify_comment_responded}checked{/if}>
                        <label for="notify_comment_responded" class="notifications_email_label">Send me an email when someone responds to a post I've commented on</label>
                    </div>
                    <div class="email_settings_div">
                        <input class='change_email_setting' data-setting='notify_product_update' id="notify_product_update" type="checkbox" {if $notify_product_update}checked{/if}>
                        <label for="notify_product_update" class="notifications_email_label">Send me an email on product updates from ShareBloc</label>
                    </div>
                    <div class="email_settings_div">
                        <input class='change_email_setting' data-setting='notify_suggestion' id="notify_suggestion" type="checkbox" {if $notify_suggestion}checked{/if}>
                        <label for="notify_suggestion" class="notifications_email_label">Send me email suggestions based on my preferences</label>
                    </div>
                </div>
            </div>

            {if $autopost_allowed}
                {* publisher tab *}
                <div class="user_settings_part {if !($active_tab == 'pubtools_tab')}hide{/if}" id="pubtools_part">
                    <div class="user_notifications_title">Publisher Tools</div>
                    <div class="subtabs_btns_div">
                        <a class="switch_subtub post_type_link fleft {if $publisher_div=='post'}active{/if}" data-subtab="publisher_post">Post preferences</a>
                        <a class="switch_subtub post_type_link fleft {if $publisher_div=='twitter'}active{/if}" data-subtab="publisher_twitter">Twitter Integration (coming soon)</a>
                        <div class="clear"></div>
                    </div>
                    {* posts subtab *}
                    <div id="publisher_post" class="subtab_div {if $publisher_div!=='post'}hide{/if}">
                        <form id="edit_pubpost">
                            <div class="publisher_option_row">
                                <div class="publisher_checkbox_row">
                                    <input id="default_tag_chk" name="default_tag_chk" type="checkbox" class="chk_valign" {if $user_info.autopost_tag_id}checked{/if}/>
                                    <label for="default_tag_chk">Default my posts to this bloc</label>
                                </div>
                                <div class="publisher_input_row">
                                    <select id="auto_bloc_select" name="autopost_tag_id" size="5" {if !$user_info.autopost_tag_id}disabled{/if}>
                                        {foreach from=$categories_structure item=category}
                                            <option {if $category.id==$user_info.autopost_tag_id}selected{/if} value="{$category.id}">{$category.name}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="publisher_option_row">
                                <div class="publisher_checkbox_row">
                                    <input id="autopost_rss_chk" name="f_autopost" type="checkbox" class="chk_valign" {if $user_info.f_autopost}checked{/if}/>
                                    <label for="autopost_rss_chk">Auto-post from my RSS feed</label>
                                </div>
                                <div class="publisher_input_row">
                                    <label class="publisher_rss_label">RSS</label>
                                    <div class="profile_edit_field" id="edit_field_first_name">
                                        <input type="text" class="join_input" name="rss" value="{$user_info.rss|escape}">
                                        <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                                    </div>
                                    <div id="pubpost_app_error_div" class="app_error">
                                        Sorry, check your errors.
                                    </div>
                                    <div id="pubpost_success_div" class="app_success">
                                        Settings have been saved.
                                    </div>
                                    <a class="sign_btn" id="pubtools_post_save_button" href="#">Save</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    {* twitter subtab *}
                    <div id="publisher_twitter" class="subtab_div {if $publisher_div!=='twitter'}hide{/if}">
                        <div class="email_settings_div">
                            <label class="notifications_email_label">Coming soon</label>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
        <div class="clear"></div>
    </div>
</div>
{else}
    {* todo bear - made quickly, straight-forward. Should review later *}
<div class="profile_header_container">
    <div class="page_sizer_wide edit_profile_container">
        {* buttons *}
        <div class="edit_profile_header">
            <div class="you_are_aditing_div">You are editing this profile</div>
            <div class="fright">
                <div class="action_button profile_edit"><a href="{$user_info.my_url}">Cancel</a></div>
                <div class="action_button profile_edit_save" id="profile_save_button">Save Changes</div>
            </div>
            <div class="clear"></div>
        </div>
        {* edit fields *}
        <form class="" id="edit_profile">
            <div class="fleft">
                <div class="profile_edit_field" id="edit_field_first_name">
                    <div class="edit_profile_field_title">First Name:</div>
                    <input type="text" class="join_input" name="first_name" value="{$user_info.first_name|escape}">
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_last_name">
                    <div class="edit_profile_field_title">Last Name:</div>
                    <input type="text" class="join_input" name="last_name" value="{$user_info.last_name|escape}">
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_position">
                    <div class="edit_profile_field_title">Position:</div>
                    <input type="text" class="join_input" name="position" value="{$user_info.position|escape}">
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_company_name">
                    <div class="edit_profile_field_title">Company:</div>
                    <input type="text" class="join_input" name="company_name" id='company_name' value="{$user_info.company.vendor_name|default|escape}">
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_location">
                    <div class="edit_profile_field_title">Location:</div>
                    <input type="text" class="join_input" name="location" value="{$user_info.location|escape}">
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_about">
                    <div class="edit_profile_field_title">
                        My Byline:<br>
                        <span>(<span id="about_counter">{$user_info.about|escape|count_characters:true}</span>/{$max_about_length})</span>
                    </div>
                    <textarea class="join_input" name="about" id="about">{$user_info.about|escape}</textarea>
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_logo">
                    <div class="edit_profile_field_title">User Photo:</div>
                    <div class="image_upload_div {if empty($user_info.logo_hash)}no_logo{/if}" style="background-image:url('{$user_info.logo.my_url_thumb}');">
                        <div class="image_upload_btn" id="user_logo_upload" data-entityType="user" data-entityID="{$user_info.user_id}"/></div>
                    </div>
                </div>
            </div>
            <div class="fright edit_profile_right">
                <div class="profile_edit_field" id="edit_field_website">
                    <div class="edit_profile_field_title">Website:</div>
                    <input type="text" class="join_input" name="website" value="{$user_info.website}">
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_linkedin">
                    <div class="edit_profile_field_title">LinkedIn:</div>
                    <input type="text" class="join_input" name="linkedin" value="{$user_info.linkedin}">
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_facebook">
                    <div class="edit_profile_field_title">Facebook:</div>
                    <input type="text" class="join_input" name="facebook" value="{$user_info.facebook}">
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_twitter">
                    <div class="edit_profile_field_title">Twitter:</div>
                    <input type="text" class="join_input" name="twitter" value="{$user_info.twitter}">
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_google_plus">
                    <div class="edit_profile_field_title">Google Plus:</div>
                    <input type="text" class="join_input" name="google_plus" value="{$user_info.google_plus}">
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_description">
                    <div class="edit_profile_field_title">Summary:</div>
                    <textarea class="join_input" name="description">{$user_info.description|escape}</textarea>
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
            </div>
            <div class="clear"></div>
        </form>
        <div id="profile_error_div" class="app_error">
            Sorry, check your errors.
        </div>
        <div id="profile_success_div" class="app_success">
            Your profile has been changed, redirecting...
        </div>
    </div>
</div>
{/if}

{include file='components/js_common.tpl'}
<script src="/js/jquery.multiselect.js"></script>
<script>
    var my_url = '{$user_info.my_url}';
    var loading = false;
    // todo move this to utils.js
    var autocomplete_vendors = {
            source: function(request, response) {
                $.ajax({
                    url: "/autocomplete.php",
                    dataType: "jsonp",
                    deferRequestBy: 300,
                    data: {
                        only_companies: 0,
                        featureClass: "search[vendor]",
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
            select: function(event, data)
            {
                $(this).val(data.item.label);

                return false;
            },
            open: function() {
            },
            close: function() {
            }
        };

    $(document).ready(function() {
        prepareImagesUpload();
        $("#company_name").autocomplete(autocomplete_vendors);
        $("#about").addSymbolsCounter();

        $(".change_email_setting").click(function() {
            changeEmailSetting($(this));
        });

        $("#notifications_clear_lnk").click(function() {
            clearNotifications();
            return false;
        });

        $(".sign_social_btn.active_btn").hover(
                function() {
                    changeBtnText($(this), 'disconnect');
                },
                function() {
                    changeBtnText($(this), 'connect');
                }
        );

        $(".account_menu_link").click(function() {
            var active_tab_id = $(this).attr('id');
            active_tab_id = active_tab_id.replace('_tab', '');
            $(".account_menu_link").removeClass('active');
            $(this).addClass('active');
            $(".user_settings_part").hide();
            $("#" + active_tab_id + "_part").show();
            return false;
        });

        $(".sign_social_btn").click(function() {
            if ($(this).hasClass('active_btn')) {
                networkDisconnect($(this).attr('data-provider'));
                return false;
            }
            return true;
        });

        $("#account_save_button").click(function() {
            changeUserData();
            return false;
        });

        $("#profile_save_button").click(function() {
            saveProfileData();
            return false;
        });

        $("#password_save_button").click(function() {
            changePassword();
            return false;
        });

        $("#pubtools_post_save_button").click(function() {
            savePubPostSettings();
            return false;
        });

        $(".switch_subtub").click(function() {
            showSubtab($(this));
            return false;
        });

        $("#auto_bloc_select").multiselect({
            multiple: false,
            header: false,
            minWidth: 265
        });

        $("#default_tag_chk").click(function() {
            if (this.checked) {
                $("#auto_bloc_select").multiselect("enable");
            } else {
                $("#auto_bloc_select").multiselect("disable");
            }
        });

    });

    function showSubtab(btn) {
        btn.siblings(".switch_subtub").removeClass('active');
        btn.addClass('active');

        var subtab_div_id = btn.attr('data-subtab');
        btn.parents(".subtabs_btns_div").siblings(".subtab_div").hide();
        btn.parents(".subtabs_btns_div").siblings("#" + subtab_div_id).show();
    }

    function changeBtnText(element, changeTo) {
        var provider = element.attr('data-displayName');
        var link_text_element = element.find(".sign_social_media_text");
        if (changeTo === 'disconnect') {
            link_text_element.text("Disconnect from " + provider);
        } else {
            link_text_element.text("Connected with " + provider);
        }
    };

    function changePassword() {
        $.ajax({
            url: '/cmd.php?cmd=changePassword',
            data: $("#password_part").serialize(),
            success: function(data) {
                if (data.status === 'success') {
                    if (data.errors) {
                        processErrors(data.errors, 'password_part');
                        return;
                    } else {
                        $("#password_part").trigger('reset');
                        $("#password_app_error_div").hide();
                        $("#password_success_div").show();
                        hideErrors('password_part');
                    }

                } else {
                    alert(data.message);
                }
            }
        });
    }

    function processErrors(errors, form_id) {
        if (errors) {
            if (form_id === 'password_part') {
                $("#password_app_error_div").show();
                $("#password_success_div").hide();
            } else if (form_id === 'edit_profile') {
                $("#profile_error_div").show();
                $("#profile_success_div").hide();
            } else {
                $("#account_app_error_div").show();
                $("#account_success_div").hide();
            }
        }

        $("#" + form_id + " .join_field_status_img").attr('src', '/images/input_ok.png').attr('title', '').show();
        for (var key in errors) {
            var name = errors[key]['name'];
            $("#edit_field_" + name).find(".join_field_status_img")
                    .attr('src', '/images/input_error.png')
                    .attr('title', errors[key]['msg']);
        }
    }

    function saveProfileData() {
        $.ajax({
            url: '/cmd.php?cmd=saveProfile',
            data: $("#edit_profile").serialize(),
            success: function(data) {
                if (data.status !== 'success') {
                    alert(data.message);
                    return;
                }
                if (data.errors) {
                    processErrors(data.errors, 'edit_profile');
                    return;
                } else {
                    $("#profile_error_div").hide();
                    $("#profile_success_div").show();
                    hideErrors('edit_profile');
                    if ($(location).attr('href') !== data.redirect_url) {
                        $(location).attr('href', data.redirect_url);
                    }
                }
            }
        });
    }

    function changeUserData() {
        $.ajax({
            url: '/cmd.php?cmd=changeUserSettings',
            data: $("#account_part").serialize(),
            success: function(data) {
                if (data.status === 'success') {
                    if (data.errors) {
                        processErrors(data.errors, 'account_part');
                        return;
                    } else {
                        $("#account_app_error_div").hide();
                        $("#account_success_div").show();
                        hideErrors('account_part');
                        if ($(location).attr('href') !== data.redirect_url) {
                            $(location).attr('href', data.redirect_url);
                        }
                    }

                } else {
                    alert(data.message);
                }
            }
        });
    }

    function savePubPostSettings() {
        if (loading) {
            return;
        }
        loading = true;
        $("#pubpost_app_error_div").hide();
        $("#pubpost_success_div").hide();

        $.ajax({
            url: '/cmd.php?cmd=savePubPostSettings',
            data: $("#edit_pubpost").serialize(),
            success: function(data) {
                loading = false;
                if (data.status !== 'success') {
                    $("#pubpost_app_error_div").text(data.message).show();
                    $("#pubpost_success_div").hide();
                    return;
                }
                $("#pubpost_app_error_div").text('').hide();
                $("#pubpost_success_div").show();
            }
        });
    }

    function hideErrors(form_id) {
        $("#" + form_id + " .join_field_status_img").attr('src', '/images/input_ok.png').attr('title', '').hide();
    }

    function networkDisconnect(provider) {
        $.ajax({
            data: {
                cmd: 'oauth_disconnect',
                provider: provider
            },
            success: function(data) {
                if (data.status === 'success') {
                    //modify URL to open networks tab
                    location.href = my_url + '/account?active_tab=networks_tab';
                }
            }
        });
    }

    function changeEmailSetting(input) {
        var setting_name = input.attr('data-setting');
        var state = input.is(":checked") ? 1 : 0;

        $.ajax({
            data: {
                cmd: 'changeUserEmailSetting',
                setting_name: setting_name,
                state: state
            },
            success: function(data) {
                if (data.status !== 'success') {
                    alert(data.message);
                }
            }
        });
    }

    function clearNotifications() {
        $.ajax({
            data: {
                cmd: 'clearNotifications'
            },
            success: function(data) {
                if (data.status === 'success') {
                    $(".notification_line").remove();
                } else {
                    alert(data.message);
                }
            }
        });
    }

</script>

{include file='components/footer_new.tpl'}