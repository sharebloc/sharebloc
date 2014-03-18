{include file='header.tpl'}
<div id="container">
    <div id="sizer">
        {include file='menu.tpl'}
        <div class="page_block">

            <ul class="nav nav-tabs">
                <li><a href="/import_file.php">Import</a></li>
                <li class="active">
                    <a href="/users.php">Accounts</a>
                </li>
                <li><a href="/results.php">Results & Export</a></li>
            </ul>

            <div class="pageTitle">Users management</div>

            <table class="users_table">
                <th class="user_name_column">Email</th>
                <th class="company_column">Company</th>
                <th class="checkbox_column">Admin</th>
                <th class="checkbox_column">Active</th>
                <th class="buttons_column"></th>
                {foreach from=$users item=user}
                <tr>
                    <form id="user_form_{$user.id}" action="/users.php" method="POST">
                        <input type="hidden" id="update_user" name="update_user" value="true">
                        <input type="hidden" id="user_id" name="user_id" value="{$user.id}">
                        <td>
                            <span class="email text_as_link" id="email_{$user.id}" data-userId={$user.id}>{$user.email}</span>
                            <div class="hide" id="div_new_email_{$user.id}">
                                <input class="user_input_text" type="text" id="new_email_{$user.id}" name="new_email_{$user.id}" value="{$user.email}">
                                <input class="user_input_text" type="text" id="new_pass_{$user.id}" name="new_pass_{$user.id}" value="" placeholder="New password">
                            </div>
                        </td>
                        <td>
                            <span id="company_{$user.id}">{$user.company}</span>
                            <div class="hide" id="div_new_company_{$user.id}" >
                                <input type="text" id="new_company_{$user.id}" name="new_company_{$user.id}" value="{$user.company}">
                            </div>
                        </td>
                        <td>
                            <input type="checkbox" id="is_admin_{$user.id}" name="is_admin_{$user.id}" disabled="disabled" {if $user.is_admin} checked {/if}>
                            <input type="hidden" id="db_is_admin_{$user.id}" name="db_is_admin_{$user.id}" value="{$user.is_admin}">
                        </td>
                        <td>
                            <input type="checkbox" id="is_active_{$user.id}" name="is_active_{$user.id}" disabled="disabled" {if $user.is_active} checked {/if}>
                            <input type="hidden" id="db_is_active_{$user.id}" name="db_is_active_{$user.id}" value="{$user.is_active}">
                        </td>
                        <td>
                            <a class="btn hide saveBtn" id="save_btn_{$user.id}" data-userId="{$user.id}" href="javascript:void(0)">Save</a>
                            <a class="btn hide cancelBtn" id="cancel_btn_{$user.id}" data-userId={$user.id} href="javascript:void(0)">Cancel</a>
                        </td>
                    </form>
                </tr>
                {/foreach}
            </table>


        </div>
    </div>
</div>

{include file='js_common.tpl'}
<script>

    var user_is_in_editing = false;

    $(document).ready(function() {

        $('.email').click(function(){

            if (user_is_in_editing) {
                return false;
            } else {
                user_is_in_editing = true;
            }

            $(this).hide();
            user_id = $(this).attr('data-userId');
            //$("#company_"+user_id).hide();
            //$("#div_new_company_"+user_id).show();
            $("#new_company_"+user_id).val($("#company_"+user_id).val());
            $("#div_new_email_"+user_id).show();
            $("#save_btn_"+user_id).show();
            $("#cancel_btn_"+user_id).show();

            $("#is_admin_"+user_id).removeAttr("disabled");
            $("#is_active_"+user_id).removeAttr("disabled");
        });

        $('.cancelBtn').click(function(){

            user_is_in_editing = false;

            $(this).hide();
            user_id = $(this).attr('data-userId');
            $("#email_"+user_id).show();
            $("#div_new_email_"+user_id).hide();
            $("#company_"+user_id).show();
            $("#div_new_company_"+user_id).hide();
            $("#save_btn_"+user_id).hide();

            var is_admin = $("#db_is_admin_"+user_id).val();
            var is_active = $("#db_is_active_"+user_id).val();

            if (is_admin === '1') {
                $("#is_admin_"+user_id).attr("checked", "checked");
            } else {
                $("#is_admin_"+user_id).removeAttr("checked");
            }
            if (is_active === '1') {
                $("#is_active_"+user_id).attr("checked", "checked");
            } else {
                $("#is_active_"+user_id).removeAttr("checked");
            }
            $("#is_admin_"+user_id).attr("disabled", "disabled");
            $("#is_active_"+user_id).attr("disabled", "disabled");
        });

        $('.saveBtn').click(function(){
            user_id = $(this).attr('data-userId');
            if (!$("#new_email_"+user_id).val().trim()) {
                alert ("Please enter the email value for editing user");
                return false;
            }

            $("#user_form_"+user_id).submit();
        });

    });
</script>

{include file='footer.tpl'}