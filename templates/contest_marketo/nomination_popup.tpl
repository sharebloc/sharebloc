<div id="popup_nominate_link_container" class="standard_popup_container hide">
    <div class="standard_popup wide">
        <div class="title_wide_popup">
            Post a link
        </div>
        <div id="nominate_link_div" class="standard_popup_content">
            <form id="nominate_link_form">
                <div class="post_field_container">
                    <div class="post_field_title">
                        <label name="title">URL: </label>
                        <img id="loader" class="post_loader_img fright hide" src="/images/loading.gif"></span>
                    </div>
                    <input id="url_input" class="validate[required] post_field_link_input" type="text" name=""/>
                </div>

                <div class="post_field_container">
                    <div class="post_field_title">
                        <label name="title">Title: </label>
                        <span class="symbols_count"><span id="title_input_counter">0</span>/150</span>
                    </div>
                    <input id="title_input" class="mentioned_field validate[required,maxSize[150]] post_field_title_input" type="text" name=""/>
                    <div class="post_image_and_text_container">
                        <div class="post_image_container">
                            <a id="post_image_link" href="javascript:void(0)"><img id="post_image" class="post_image" src=""></a>
                            <div class="post_image_selector">
                                <span id ="img_prev" class="post_image_selector_arrow hide">&lt;</span>
                                <span id="img_current" class="hide">0</span>
                                <span id="img_of_word">No images</span>
                                <span id="img_count" class="hide">0</span>
                                <span id ="img_next" class="post_image_selector_arrow hide">&gt;</span>
                            </div>
                        </div>
                    </div>
                    <div class="post_text_container">
                        <div class="post_field_title">
                            <label name="title">Text (optional): </label>
                            <span class="symbols_count"><span id="text_input_counter">0</span>/500</span>
                        </div>
                        <textarea id="text_input" class="mentioned_field validate[maxSize[500]] post_field_text_input" name=""/></textarea>
                        <br>
                        <input id="no_thumb_chk" class="post_no_thumb_chk" type="checkbox" name=""/>
                        <label class="no_thumb_text">No thumbnail?</label>
                    </div>
                    <div class="clear"></div>
                </div>

            </form>
        </div>
        <div id="nominate_link_buttons_div" class="popup_function">
            <a id="nominate_link_submit" class="save_changes post_contest" href="#">Post</a>
            {if $logged_in && $user_info.f_get_sponsor_email == 0}
                <input id="get_sponsor_email_chk" class="post_no_thumb_chk" type="checkbox" name="" checked="checked">
                <span class="">Opt into one promotional email from each of our sponsors?</span>
            {/if}
            <a class="cancel_btn cancel" href="#">Cancel</a>
        </div>
    </div>
</div>