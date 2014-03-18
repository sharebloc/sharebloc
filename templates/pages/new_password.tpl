{include file='components/header_new.tpl'}

<div align="center" class="content_div">
    <form action="/pw_rst/{$password_reset_key}" method="POST" id="password_form" class="sign_form">
        <input type="hidden" id="password_reset_key" name="password_reset_key" value="{$password_reset_key}" />

        <div class="content_block">
            <div class="filler">
            </div>

            <div class="content_whole">
                <div id="page_title" class="system_message {if $password_reset_done || $bad_reset_key}centered{/if}">
                    {if $password_reset_done}
                        You Have Changed Your Password!<br>
                        <a href="/">Back to the Homepage</a>
                    {elseif $bad_reset_key}
                        Your Reset Key is Invalid.
                    {else}
                        Choose Your New Password
                    {/if}
                </div>

                {if !$password_reset_done && !$bad_reset_key}
                    <div id="account_block">
                        <div clss="join_field_row">
                            <div class="join_field_title">New Password</div>
                            <input id="new_password" type="password" class="join_input" type="text" name="new_password"/>
                        </div>
                        <div clss="join_field_row">
                            <div class="join_field_title">Verify Password</div>
                            <input id="verify_password" type="password" class="join_input" type="text" name="verify_password"/>
                        </div>
                        <div id="error_message_div" class="app_error {if !$error_message}hide{/if}">
                            <span id="error_message">{$error_message}</span>
                        </div>
                        <div class="join_field_row">
                            <a id="submit_passw_button" class="sign_btn" href="javascript:void(0)">Submit</a>
                        </div>
                    </div>
                {/if}
            </div>
            <div class="bottom_filler">
            </div>

        </div>
    </form>
</div>

{include file='components/js_common.tpl'}
<script>
    $(document).ready(function() {
        $("#submit_passw_button").click(function() {
            var err_msg = '';

            var new_password = $.trim($("#new_password").val());
            var verify_password = $.trim($("#verify_password").val());
            if (!new_password || !verify_password) {
                err_msg = "Please fill both fields";
            } else if (new_password !== verify_password) {
                err_msg = "Please check your passwords, they are not equal.";
            }

            if (err_msg) {
                $("#error_message").text(err_msg);
                $("#error_message_div").show();
                return false;
            }

            $("#password_form").submit();
        });

        $('.join_input').keypress(function(e) {
            if (e.which === 13) {
                $('#submit_passw_button').click();
            }
        });
    });
</script>

{include file='components/footer_new.tpl'}