{include file='components/header_new.tpl'}

<div class="profile_header_container">
    <div class="page_sizer_wide">
        <div class="events_header_div">
            <div class="events_header fleft">{$page_tag.name} Events</div>
            <div id="new_event_btn" class="action_button profile_follow right_btn"><a href="">Suggest an Event</a></div>
            <div class="clear"></div>
        </div>
    </div>
</div>
<div class="page_sizer_wide rails_container">
    <div class="left_rail">
        <div class="content_container">

            <div class="more_container"
                data-offsetForMore="{$events_on_page}"
                data-noMore="{$no_more_content}"
                data-pageType="calendar"
                data-entityID="{$page_tag.tag_id}">
                {if $content}
                    {foreach from=$content item=event}
                        {include file='components/front/event.tpl'}
                    {/foreach}
                {else}
                    There are no Events.
                {/if}
            </div>
            <div class="front_loader_div hide"><img src="/images/loading.gif"></div>
        </div>
    </div>
    <div class="right_rail">
        <div class="right_rail_content">
            {include file='components/front/invite_link.tpl' type='event'}
        </div>
    </div>
    <div class="clear"></div>
</div>
<input type="hidden" id="tag_id" name="tag_id" value="{$page_tag.tag_id}">
{include file='components/front/suggest_event_popup.tpl'}
{include file='components/front/edit_event_popup.tpl'}
{include file='components/js_common.tpl'}
<script>
    var mentions = { };
    var mention_count = 0;
    var need_refresh = false;


    $(document).ready(function() {
        setRightRailFixed();
        prepareCopyToClipboard();
        prepareSharing();
        preparePageForMore();
        prepareSuggestion();
        prepareMentions();
    });

    function prepareMentions() {
        $(".mentioned_field").keypress(function(event) {
            if (event.which === 32 || event.which === 46 || event.which === 13) {
                processMentions($(this));
            }
        });

        $(".mentioned_field").blur(function() {
            processMentions($(this));
        });

        $(".mention_delete").click(function() {
            $vendor_id = $(this).attr('data-vendorId');
            if ($(this).parents('.mentioned_tag').hasClass('template')) {
                return false;
            }
            $(this).parents('.mentioned_tag').remove();
            mentions[$vendor_id] = 0;
            mention_count--;
        });
    }

    function prepareSuggestion() {
        $("#new_event_btn").click(function() {
            if(!is_logged) {
                $(location).attr('href', login_url);
                return false;
            }
            $("#suggest_event_inputs").show();
            $("#suggest_event_container").show();
            $("#suggest_event_success").hide();
            $("#suggest_event_popup_close").text("Cancel");
            $("#suggest_event_btn").show();
            return false;
        });

        $("#suggest_event_popup_close, #edit_event_popup_close").click(function() {
            var popup_type = $(this).attr("data-popupType");

            $("#"+popup_type+"_form").trigger('reset');
            $("#"+popup_type+"_container").hide();
            $("#"+popup_type+"_inputs").hide();

            $(".mention_delete").trigger('click');

            if (need_refresh) {
                location.reload();
            }
            return false;
        });

        $("#suggest_event_btn, #edit_event_btn").click(function() {
            var popup_type = $(this).attr("data-popupType");
            if (popup_type === 'suggest_event') {
                postEvent(popup_type);
            } else {
                editEvent(popup_type);
            }
            return false;
        });

        $(".delete_event").click(function() {
            if (!confirm("Confirm you want to delete. Are you sure?")) {
                return false;
            }
            var event_id = $(this).parents(".event_container").attr("data-postId");
            deleteEvent(event_id)
            return false;
        });

        $(".appr_status_edit").click(function() {
            var event_id = $(this).parents(".event_container").attr("data-postId");

            $("#event_id").val(event_id);

            getEventData(event_id);

            $("#edit_event_inputs").show();
            $("#edit_event_container").show();
            $("#edit_event_success").hide();
            $("#edit_event_popup_close").text("Cancel");
            $("#edit_event_btn").show();
            return false;
        });
    }

    function editEvent(popup_type) {
        var data = { };
        data.event_id = $('#event_id').val();
        data.name = $('#edit_event_form #suggestion_name').val();
        data.url = $('#edit_event_form #suggestion_url').val();
        data.start_date = $('#edit_event_form #suggestion_start_date').val();
        data.end_date = $('#edit_event_form #suggestion_end_date').val();
        data.location = $('#edit_event_form #suggestion_location').val();
        data.month = $('#edit_event_form #suggestion_month').val();
        data.tag_id = $('#tag_id').val();

        data.vendors = getVendors();
        data.subtag_id = $('#category_input').val();

        sendEventData(data, popup_type);
    }

    function postEvent(popup_type) {
        var data = { };
        data.name = $('#suggestion_name').val();
        data.url = $('#suggestion_url').val();
        data.start_date = $('#suggestion_start_date').val();
        data.end_date = $('#suggestion_end_date').val();
        data.location = $('#suggestion_location').val();
        data.tag_id = $('#tag_id').val();

        sendEventData(data, popup_type);
    }

    function sendEventData(data, popup_type) {
        data.cmd = 'processEvent';
        $.ajax({
            data: data,
            success: function(data) {

                if (data.status === 'success') {
                    $("#"+popup_type+"_inputs").hide();
                    $("#"+popup_type+"_success").show();
                    $("#"+popup_type+"_btn").hide();
                    $("#"+popup_type+"_popup_close").text("Close");
                    need_refresh = true;
                } else {
                    alert(data.message);
                }
            }
        });
    }

    function getEventData(event_id) {
        $.ajax({
            data: {
                cmd: 'getEventData',
                event_id: event_id
            },
            success: function(data) {
                if (data.status === 'success') {
                    fillEditForm(data.event);
                } else {
                    alert(data.message);
                }
            }
        });
    }
    function deleteEvent(event_id) {
        $.ajax({
            data: {
                cmd: 'delEvent',
                event_id: event_id
            },
            success: function(data) {
                if (data.status === 'success') {
                    $("#event_"+event_id).remove();
                } else {
                    alert(data.message);
                }
            }
        });
    }
    function fillEditForm(event) {
        $('#event_id').val(event.event_id);
        $('#edit_event_form #suggestion_name').val(event.name);
        $('#edit_event_form #suggestion_url').val(event.url);

        if (event.f_approved === '1') {
            $('#edit_event_form #suggestion_start_date').val(event.start_ts);
            $('#edit_event_form #suggestion_end_date').val(event.end_ts);
        } else {
            $('#edit_event_form #suggestion_start_date').val(event.start_date);
            $('#edit_event_form #suggestion_end_date').val(event.end_date);
        }

        $('#edit_event_form #suggestion_location').val(event.location);

        if (event.subtag_id) {
            $('#category_input').val(event.subtag_id);
        }

        for(var i=0; i<event.vendors.length; i++) {
            addMentionedVendor(event.vendors[i])
        }

    }

    function getVendors() {
        var vendors = [];
        for (var key in mentions) {
            if (mentions[key]) {
                vendors.push(key);
            }
        }
        return vendors;
    }

    function processMentions(input_el) {
        $question_text = input_el.val();
        $.ajax({
            data: {
                cmd: 'check_mentions',
                question_text: encodeURIComponent($question_text)
            },
            success: function(data) {
                if (data.status === 'success' && data.message) {
                    if (mentions[data.message.vendor_id] !== 1) {
                        addMentionedVendor(data.message);
                    }
                }
            }
        });
    }

    function addMentionedVendor(vendor_data) {
        var new_div = $(".mentioned_tag.template").clone(true);

            new_div.removeClass('template');
            new_div.attr('id', "mention_tag_" + vendor_data.vendor_id);
            new_div.find(".mentioned_tag_title").text(vendor_data.vendor_name);
            new_div.find(".mentioned_tag_x").attr('data-vendorId', vendor_data.vendor_id);
            $('#mentions_div').append(new_div);
            new_div.show();

            mentions[vendor_data.vendor_id] = 1;
            mention_count++;
    }

</script>
{include file='components/footer_new.tpl'}