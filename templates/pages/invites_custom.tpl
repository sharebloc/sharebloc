{include file='components/header_new.tpl' hide_search='1'}

<div align="center">
    <div class="custom_invite_page_title">Custom invites</div>

    <div class="custom_invite_add_div">
        <div class="custom_invite_add_title">Add new invite</div>
        <label>Invite key:</label>
        <input id="confirm_key" class="standard_input" type="text" placeholder="Invite key" value=""/>
        <div class="custom_invite_result_div">Result url will be: <span class="custom_invite_result">{$base_url}/invite/<span id="current_key"></span></span></div>
        <label class="custom_invite_textarea_label">Comment</label>
        <textarea class="standard_input standard_textarea" id="comment" placeholder="optional"></textarea>
        <br>
        <div id="submit_custom_invite" class="orange_button" id="comment">Add</div>
    </div>

    <div class="existing_custom_invites_title">Existing invites</div>

    <table class="invites_front_list">
        <tr class="invites_header">
            <td>Invite key</td>
            <td>Comment/User full name</td>
            <td>Invite created/Invite used</td>
            <td>Result url/User email</td>
        </tr>
        {foreach $custom_invites item=invite}
            <tr class="user_row">
                <td>{$invite.confirm_key}</td>
                <td>{$invite.comment}</td>
                <td>{$invite.created_ts|date_format:"%b, %d"}</td>
                <td><a href="{$invite.my_url}">{$invite.my_url}</a></td>
            </tr>
            {foreach $invite.users item=user}
                <tr class="invited_row">
                    <td class="no_border_cell">&nbsp;</td>
                    <td><a href="{$user.my_url}">{$user.full_name}</a></td>
                    <td>{$user.invite_used_ts|date_format:"%b, %d"}</td>
                    <td>{$user.email}</td>
                </tr>
            {/foreach}
            <tr class="invites_spacer_tr">
                <td colspan="7" class="no_border_cell">&nbsp;</td>
            </tr>
        {/foreach}
    </table>
</div>
{include file='components/js_common.tpl'}
<script>
    $(document).ready(function() {
        $("#submit_custom_invite").click(function() {
            addNewInvite();
            return false;
        });
        $("#confirm_key").keyup(function() {
            $("#current_key").text($("#confirm_key").val().replace(/ /g, '_'));
        });
    });
</script>
{include file='components/footer_new.tpl'}