<div id="popup_invite_front_container" class="standard_popup_container hide">
    <div class="standard_popup">
        <div class="title_wide_popup">Invite to private beta</div>
            <div id="invite_front_inputs" class="standard_popup_content hide">
                <form id="front_invite_form">
                    <div class="popup_input_row">
                        <label>First Name:</label>
                        <input id="front_input_first_name" name="first_name" class="validate[required,maxSize[64]] textbox join_input" placeholder="First Name" value=""/>
                    </div>
                    <div class="popup_input_row">
                        <label>Last Name:</label>
                        <input id="front_input_last_name" name="last_name" class="validate[required,maxSize[64]] textbox join_input" placeholder="Last Name" value=""/>
                    </div>
                    <div class="popup_input_row">
                        <label>Email:</label>
                        <input id="front_input_email" name="email" class="validate[custom[email],required,maxSize[64]] textbox join_input" placeholder="Add Email" value=""/>
                    </div>
                    <div class="popup_input_row">
                        <label id="front_input_email_label">Email text:</label>
                        <textarea id="front_input_text" name="text" class="validate[required,maxSize[500]] join_input">Hello,
{if !empty($user_info.first_name)}{$user_info.first_name}{/if} {if !empty($user_info.last_name)}{$user_info.last_name}{/if} invites you try out the private beta for ShareBloc. ShareBloc helps you discover better business content with other professionals like you.</textarea>
                    </div>
                </form>
            </div>
            <div id="front_invite_success" class="standard_popup_content hide">
                Your invite was successfully sent!
            </div>
        <div class="popup_function">
            <a id="invite_front_send" class="save_changes" href="#">Invite</a>
            <a id="invite_front_popup_close" class="cancel" href="#">Cancel</a>
        </div>
    </div>
</div>