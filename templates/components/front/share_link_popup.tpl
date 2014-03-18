<div id="popup_share_link_container" class="standard_popup_container hide">
    <div class="standard_popup">
        <div id="share_link_title" class="title_wide_popup">Share Post</div>
            <div id="share_link_inputs" class="standard_popup_content hide">
                <form id="share_link_form">
                    <div class="popup_input_row">
                        <label>Email:</label>
                        <input id="share_link_email" name="email" class="validate[custom[email],required,maxSize[64]] textbox join_input" placeholder="Add Email" value=""/>
                    </div>
                    <div class="popup_input_row">
                        <label id="share_link_email_label">Email text:</label>
                        <textarea id="share_link_text" name="share_link_text" class="validate[required,maxSize[500]] join_input"></textarea>
                    </div>
                </form>
            </div>
            <div id="share_link_success" class="standard_popup_content hide">
                Your email with link to share was successfully sent!
            </div>
        <div class="popup_function">
            <a id="share_link_send" class="save_changes" href="#">Share</a>
            <a id="share_link_popup_close" class="cancel" href="#">Cancel</a>
        </div>
    </div>
</div>