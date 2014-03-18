{include file='components/header_new.tpl'}

<div class="page_sizer">
    {include file='components/join_steps.tpl'}
    <div class="content_container join_step_container">
        <div class="join_container_block page_title"><span class="page_title_text">Join</span></div>
        <div class="join_container_block buttons_section">
            <div class="social_btns_div">
                <a class="sign_social_btn {if $provider=='linkedin'}active_btn{/if}" href="/ext_auth.php?provider=linkedin&type=join">
                    <img class="sign_logo" src="/images/linkedin.png">
                    <span class="sign_social_media_text">Sign Up with LinkedIn</span>
                </a>
                <a class="sign_social_btn {if $provider=='twitter'}active_btn{/if}" href="/ext_auth.php?provider=twitter&type=join">
                    <img class="sign_logo" src="/images/twitter.png">
                    <span class="sign_social_media_text">Sign Up with Twitter</span>
                </a>
                <a class="sign_social_btn {if $provider=='google'}active_btn{/if}" href="/ext_auth.php?provider=google&type=signin">
                    <img class="sign_logo" src="/images/gmail.png">
                    <span class="sign_social_media_text">Sign Up with Gmail</span>
                </a>
                <div class="clear"></div>
                {if !$provider}
                    <div class="coming_soon_text">Sign up with a social network to follow your friends</div>
                    <div class="coming_soon_text">We will never post anything without your permission.</div>
                {/if}
            </div>
        </div>
        <div class="join_container_block fields_section">
            <div class="center_title">
                <span class="subtitle">
                    {if $provider=='twitter'}
                        Signing Up with Twitter
                    {else if $provider=='linkedin'}
                        Signing Up with LinkedIn
                    {else if $provider=='google'}
                        Signing Up with Gmail
                    {else}
                        <a id="sign_up_with_email" href="javascript:void(0)">
                            Or Sign Up with Your Email
                        </a>
                    {/if}
                </span>
            </div>
            <form id="signup_form" class="sign_form {if !$provider}hide{/if}" method="POST" action="{$join_redir_path}" autocomplete="off">
                {if $provider && $image_url}
                    <div id="join_field_image" class="join_field_row">
                        <div class="join_field_title">Photo</div>
                        <div class="join_logo_div"><img src="{$image_url}"></div>
                        <div class="join_field_status">
                            <img class="join_field_status_img" src="/images/input_ok.png">
                        </div>
                    </div>
                {/if}
                {foreach $user_data key=key item=field}
                    {if ($field.type=='password' || $field.type=='image') && $provider}
                    {else}
                        <div id="join_field_{$key}" class="join_field_row">
                            <div class="join_field_title">
                                {$field.title}
                                {if $key=='about'}
                                    <span class="symbols_count"><span id="about_counter">{$field.value|escape|truncate:$max_about_length:""|count_characters:true}</span>/{$max_about_length}</span>
                                {/if}
                            </div>
                            {if $field.type=='textarea'}
                                <textarea class="join_input" id="{$key}" name="{$key}">{if $key=='about'}{$field.value|escape|truncate:$max_about_length:""}{else}{$field.value}{/if}</textarea>
                            {elseif $field.type=='password'}
                                <input type="password" class="join_input" id="{$key}" name="{$key}" value="{$field.value}">
                            {elseif $field.type=='image'}
                                <div class="image_upload_div" style="background-image:url('/images/anonymous_user_thumb.jpg');">
                                    <div class="image_upload_btn" id="image_upload" data-entityType="user" data-entityID=""/></div>
                                </div>
                            {else}
                                <input type="text" class="join_input" id="{$key}" name="{$key}" value="{$field.value}">
                            {/if}
                            <div class="join_field_status">
                                <img class="join_field_status_img" src="/images/input_ok.png">
                            </div>
                        </div>
                    {/if}
                {/foreach}
                <div id="app_error_div" class="app_error">
                    Sorry, there is something wrong with your app.
                </div>
                <a class="sign_btn" id="signup_button" href="#">Sign Up</a>
                <div class="long_text_div">
                    By clicking Sign Up, you agree to our <a class="terms_link" href="/terms">Terms</a> and that you have read our Data Use Policy, including our Cookie Use.
                </div>
            </form>

        </div>
        <div class="join_container_block have_account">
            <a href="/signin"><span class="subtitle">Already have an account?</span></a>
        </div>
    </div>
</div>

{include file='components/js_common.tpl'}
<script>
    var open_email_join = {$open_email_join};
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
        select: function(event, data)
        {
            $(this).val(data.item.label);
            return false;
        },
        open: function() { },
        close: function() { }
    };

    $(document).ready(function() {

        $("#company").autocomplete(autocomplete_companies);
        $("#about").addSymbolsCounter();
        $("#about").keydown();

        $("#sign_up_with_email").click(function(){
            $("#signup_form").show();
            $(this).text('Sign Up with Your Email');
            $(".buttons_section").hide();
        });

        $("#signup_button").click(function() {
            signUp();
            return false;
        });

        if (open_email_join) {
            $("#sign_up_with_email").click();
        }

        prepareImagesUpload();
    });

    function processErrors(errors) {

        if (errors) {
            $("#app_error_div").show();
        }

        $(".join_field_status_img").attr('src', '/images/input_ok.png').attr('title', '').show();

        for (var key in errors) {
            var name = errors[key]['name'];
            $("#join_field_"+name).find(".join_field_status_img")
                                  .attr('src', '/images/input_error.png')
                                  .attr('title', errors[key]['msg']);
        }
    }

    function signUp() {
        $.ajax({
            url: '/cmd.php?cmd=signup',
            data: $("#signup_form").serialize(),
            success: function(data) {
                    if (data.status === 'success') {
                        if (data.errors) {
                            processErrors(data.errors);
                            return;
                        }
                        $(location).attr('href', data.redirect_url);
                    } else {
                        alert(data.message);
                    }
            }
        });
    }
</script>

{include file='components/footer_new.tpl'}