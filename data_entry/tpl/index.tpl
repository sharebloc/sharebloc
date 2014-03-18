{include file='header.tpl'}
<div class="page_block">
    <div id="container">
        <div id="sizer">
            {include file='menu.tpl'}

            {if $logged_in}
                {if $is_admin}
                    <div class="pageTitle">Admin Page</div>
                    <div class="action_buttons_div">
                        <a  class="btn" href="/import_file.php">Import csv file</a>
                        <!--a class="btn" href="/update_al_data.php">Update Angel List Vendors</a-->
                    </div>
                {else}
                    <div class="pageTitle">Data Entry</div>
                {/if}
                    {if $is_admin}
                        <br>
                    {else}
                        <div class="action_buttons_div">
                            <a class="btn" href="/search_profile.php?new_vendor=true">Start</a>
                        </div>
                        <div class="text_under_btn_div">(this will take about 15 seconds)</div>
                    {/if}
            {else}

            <form class="form-horizontal" action="/index.php" method="POST" id="login_form">
                <input type="hidden" name="cmd" id="cmd" value="log_in">

                <div class="pageTitle">Log In</div>

                {if $login_error}
                    <div class="error_msg">{$login_error}</div>
                {/if}
                <div class="login_table_div">
                    <div class="control-group">
                        <label class="control-label" for="email">Email</label>
                        <div class="controls">
                            <input type="text" id="email" name="email" value="">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="password">Password</label>
                        <div class="controls">
                            <input id="password" name="password" class="login_field" type="password"  value="" />
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="signup_signin">
                            <a id="login_button" class="btn" href="javascript:void(0)">Log In</a>
                        </div>
                    </div>
                </div>
            </form>

        {/if}



    </div>
</div>
</div>
{include file='js_common.tpl'}

    <script>



    $(document).ready(function() {

        $("#login_button").click(function(){
            if (!validate()) {
                return false;
            }
            $("#login_form").submit();
        });

        $('.login_field').keypress(function (e) {
            if (e.which == 13) {
                $('#login_button').click();
            }
        });
    });

    function validate() {
        var temp = true;
        $('input').each(function() {
            if(!$(this).val()) {
                temp  = false;
                $(this).css('border', '2px solid red');
            }
        });
        return temp;
    }


    </script>


{include file='footer.tpl'}