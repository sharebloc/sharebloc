{include file='components/header_new.tpl'}

<div class="page_sizer">
    <div class="content_container join_container">
        <div class="join_container_block page_title"><span class="page_title_text">Sign In</span></div>
        <div class="join_container_block buttons_section">
            <div class="social_btns_div ">
                <a class='sign_social_btn' href="/ext_auth.php?provider=linkedin&type=signin">
                    <img class="sign_logo" src="/images/linkedin.png">
                    <span class="sign_social_media_text">Sign In with LinkedIn</span>
                </a>
                <a class='sign_social_btn' href="/ext_auth.php?provider=twitter&type=signin">
                    <img class="sign_logo" src="/images/twitter.png">
                    <span class="sign_social_media_text">Sign In with Twitter</span>
                </a>
                <a class='sign_social_btn' href="/ext_auth.php?provider=google&type=signin">
                    <img class="sign_logo" src="/images/gmail.png">
                    <span class="sign_social_media_text">Sign In with Gmail</span>
                </a>
                <div class="clear"></div>
            </div>
        </div>
        <div class="join_container_block fields_section">
            <div class="center_title">
                <a id="sign_in_with_email" href="javascript:void(0)">
                    <span class="subtitle">Or Sign In with Your Email</span>
                </a>
            </div>
            <form id="signin_form" class="sign_form hide" method="POST" action="{$login_redir_path}" autocomplete="off">
                <div id="join_field_email" class="join_field_row">
                    <div class="join_field_title">Email</div>
                    <input type="text" class="join_input" id="email" name="email" value="{if !empty($email)}{$email}{/if}">
                    <div class="join_field_status"><img class="join_field_status_img" src=""></div>
                </div>
                <div id="join_field_password" class="join_field_row">
                    <div class="join_field_title">Password</div>
                    <input type="password" class="join_input" id="password" name="password" value="">
                    <div class="join_field_status"><img class="join_field_status_img" src=""></div>
                </div>
                <div id="app_error_div" class="app_error">
                    Sorry, wrong Username/Email and password combination.
                </div>
                <div class="form_footer">
                    <div class="long_line_block">
                        <a class="sign_btn" id="signin_button" href="#">Sign In</a>
                    </div>
                    <div class="long_line_block">
                        <a id="lost_password_link" href="javascript:void(0)"><span class="subtitle">Forgot Password?</span></a>
                    </div>
                </div>
            </form>
            <div id="forgot_password_div" class="hide">
                <form class="sign_form">
                    <div class="join_field_row">
                        <div id="forgot_password_text" class="join_field_title">Enter Your Email</div>
                        <input type="text" class="join_input" id="email_forgot_pass" name="email_forgot_pass" value="">
                        <a class="sign_btn" id="reset_password_button" href="javascript:void(0)">Reset My Password</a>
                    </div>
                </form>
            </div>
            <div id="password_reset_message" class="hide"></div>
        </div>
        <div class="join_container_block have_account">
            <a href="/join"><span class="subtitle">Don't have an account? Join now!</span></a>
        </div>
    </div>
</div>

{include file='components/js_common.tpl'}
<script>
    $(document).ready(function() {
        $("#sign_in_with_email").click(function() {
            $("#signin_form").show();
        });

        $("#password").keypress(function(e) {
            if (e.which === 13) {
                $('#signin_button').trigger('click');
                return false;
            }
        });

        $("#signin_button").click(function() {
            signIn();
            return false;
        });

        $("#lost_password_link").click(function() {
            $("#sign_in_with_email").parent().hide();
            $("#signin_form").hide();
            $("#forgot_password_div").show();
        });

        $("#reset_password_button").click(function() {
            $(this).hide();
            $("#email_forgot_pass").hide();
            $("#forgot_password_text").addClass('center_title');
            $("#forgot_password_text").html('<img border="0" style="width: 32px; height: 32px;" src="/images/loading.gif">');

            sendPasswordRecoveryCommand();

        });
    });

    function sendPasswordRecoveryCommand() {
        $.ajax({
            data: {
                cmd: 'forgot_password',
                email: $("#email_forgot_pass").val()
            },
            success: function(data) {
                $("#forgot_password_div").hide();
                if (data.status === 'success') {
                    $("#password_reset_message").text('We\'ve sent password reset instructions to your email address. Be sure to check your email\'s spam and junk folders too!');
                    $("#password_reset_message").addClass('app_success').show();
                } else {
                    $("#password_reset_message").text('We could not find the account you specified.  Please contact support@sharebloc.com for assistance.');
                    $("#password_reset_message").addClass('app_error').show();
                }
            }
        });
    }

    function processErrors(errors) {

        if (errors) {
            $("#app_error_div").show();
        }

        $(".join_field_status_img").attr('src', '/images/input_ok.png').attr('title', '').show();

        for (var key in errors) {
            var name = errors[key]['name'];
            $("#join_field_" + name).find(".join_field_status_img")
                    .attr('src', '/images/input_error.png')
                    .attr('title', errors[key]['msg']);
        }
    }

    function signIn() {
        $.ajax({
            url: '/cmd.php?cmd=signin',
            data: $("#signin_form").serialize(),
            success: function(data) {
                if (data.status === 'success') {
                    if (data.errors) {
                        processErrors(data.errors);
                        return;
                    }
                    $(location).attr('href', data.redirect_url);
                } else {
                    processErrors([]);
                    alert(data.message);
                }
            }
        });
    }
</script>

{include file='components/footer_new.tpl'}