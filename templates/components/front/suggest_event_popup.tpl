<div id="suggest_event_container" class="standard_popup_container hide" data-popupType="suggest_event">
    <div class="standard_popup">
        <div class="title_wide_popup">Suggest an Event</div>
        <div id="suggest_event_inputs" class="standard_popup_content hide">
            <form id="suggest_event_form">
                <div class="popup_input_row">
                    <label>Event Name:</label>
                    <input id="suggestion_name" name="suggestion_name" class="textbox join_input" placeholder="Event Name" value=""/>
                </div>
                <div class="popup_input_row">
                    <label>URL (if available):</label>
                    <input id="suggestion_url" name="suggestion_url" class="textbox join_input" placeholder="URL" value=""/>
                </div>
                <div class="popup_input_row">
                    <label>Start Date:</label>
                    <input id="suggestion_start_date" name="suggestion_start_date" class="textbox join_input" placeholder="MM/DD/YYYY" value=""/>
                </div>
                <div class="popup_input_row">
                    <label>End Date:</label>
                    <input id="suggestion_end_date" name="suggestion_end_date" class="textbox join_input" placeholder="MM/DD/YYYY" value=""/>
                </div>
                <div class="popup_input_row">
                    <label>Location:</label>
                    <input id="suggestion_location" name="suggestion_location" class="textbox join_input" placeholder="Location" value=""/>
                </div>
            </form>
        </div>
        <div id="suggest_event_success" class="standard_popup_content hide">
            Your suggestion was successfully sent!
        </div>
        <div class="popup_function">
            <a id="suggest_event_btn" class="save_changes" href="#" data-popupType="suggest_event">Suggest</a>
            <a id="suggest_event_popup_close" class="cancel" href="#" data-popupType="suggest_event">Cancel</a>
        </div>
    </div>
</div>