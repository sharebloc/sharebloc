<div id="edit_event_container" class="standard_popup_container hide" data-popupType="edit_event">
    <div class="standard_popup">
        <div class="title_wide_popup">Edit an Event</div>
        <div id="edit_event_inputs" class="standard_popup_content hide">
            <form id="edit_event_form">
                <input type="hidden" id="event_id" name="event_id" value="">
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
                    <input id="suggestion_start_date" name="suggestion_start_date" class="textbox join_input" placeholder="YYYY-MM-DD HH:II:SS" value=""/>
                    <div class="date_format_title">Date format: YYYY-MM-DD HH:II:SS</div>
                </div>
                <div class="popup_input_row">
                    <label>End Date:</label>
                    <input id="suggestion_end_date" name="suggestion_end_date" class="textbox join_input" placeholder="YYYY-MM-DD HH:II:SS" value=""/>
                    <div class="date_format_title">Date format: YYYY-MM-DD HH:II:SS</div>
                </div>
                <div class="popup_input_row">
                    <label>Month (If no start or end date):</label>
                    <input id="suggestion_month" name="suggestion_end_date" class="textbox join_input" placeholder="MM" value=""/>
                </div>
                <div class="popup_input_row">
                    <label>Location:</label>
                    <input id="suggestion_location" name="suggestion_location" class="textbox join_input" placeholder="Add Location" value=""/>
                </div>
                <div class="popup_input_row">
                    <label>Tag:</label>
                    <select id="category_input" name="category_input" class="suggestion_tag" size="7">
                        {foreach from=$categories key=key item=tag_id}
                            {if $subcategories.$tag_id.parent_tag_id}
                                <option class="cat" value="{$subcategories.$tag_id.tag_id}">{$subcategories.$tag_id.tag_name}</option>
                            {/if}
                        {/foreach}
                    </select>
                </div>
                <div class="">
                    <div class="suggestion_mentions">Connect with a vendor (optional): </div>
                    <input id="suggestion_vendor_input" class="mentioned_field" type="text" name="" placeholder="Add a Vendor"/>
                    <div id="mentions_div" class="mentioned_vendor fleft">
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
        <div id="edit_event_success" class="standard_popup_content hide">
            Suggestion was successfully approved!
        </div>
        <div class="popup_function">
            <a id="edit_event_btn" class="save_changes" href="#" data-popupType="edit_event">Save</a>
            <a id="edit_event_popup_close" class="cancel" href="#" data-popupType="edit_event">Cancel</a>
        </div>
    </div>
</div>
<div class="mentioned_tag template hide">
    <div class="mentioned_tag_title"></div>
    <div class="mentioned_tag_x mention_delete" href="javascript:void(0)">x</div>
</div>