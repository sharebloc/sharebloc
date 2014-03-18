{include file='components/header_new.tpl'}

<div class="page_sizer">
    {include file='components/join_steps.tpl'}
    <div class="content_container join_step_container">
        <div class="join_container_block page_title"><span class="page_title_text">Invite Five of Your Contacts</span></div>
        <div class="join_container_block page_title"><span class="join_subtitle">Invite five friends or more into this private beta to follow them! </span></div>
        <div class="join_container_block fields_section">
            <form id="contacts_form" method="POST" action="{$join_redir_path}" autocomplete="off">
                <div id="join_contacts_div" class="join_contacts_div">
                    {foreach $contacts key=key item=contact}
                        <div id="join_contact_{$contact.local_id}" class="join_contact_block data_block" data-contactFullName="{$contact.full_name}" data-contactEmail="{$contact.email|escape}" data-contactID="{$contact.local_id}">
                            <div id="left_block_{$contact.local_id}" class="contact_left_block{if $contact.email} with_email{/if}">
                                {if $contact.image_url}
                                    <img class="contact_image" src="{if $contact.image_url}{$contact.image_url}{else}/images/anonymous_user.png{/if}">
                                {/if}
                                <div class="contact_details">
                                    <div class="contact_name">{$contact.full_name|escape|truncate:50:"...":true}</div>
                                    {if $contact.email}
                                        <div class="contact_email">{$contact.email|escape}</div>
                                    {/if}
                                </div>
                            </div>
                            {if !$contact.email}
                                <div class="contact_right_block">
                                    <div class="contact_right_block_content hide">
                                        <div class="contact_name">Add {$contact.first_name|escape}'s email to invite:</div>
                                        <div class="contact_input_div">
                                            <input class="join_input" placeholder="" value="{if $contact.email}{$contact.email}{/if}">
                                        </div>
                                        <div class="join_field_status">
                                            <img class="join_field_status_img" src="/images/input_ok.png">
                                        </div>
                                    </div>
                                </div>
                            {/if}
                        </div>
                    {/foreach}
                    <div class="clear"></div>
                    <div id="additional_emails" class="additional_emails">
                    </div>
                    <div id="more_emails" class="more_emails">Add More Emails</div>
                </div>
            </form>
        </div>
        <div class="join_container_block used_emails_section hide">
            <div class="used_email_div template hide">
                <div class="used_email_name"></div>
                <div class="used_email_email"></div>
                <div class="used_email_remove">Remove</div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="join_container_block fields_section">
            <div id="app_error_div" class="app_error join_invites_app_error">
                Sorry, there is something wrong with your app.
            </div>
            <a href="{$skip_redirect}" class="join_invites_skip">Skip</a>
            <a href="#" id="join_invites_submit" class="join_invites_submit">Invite and Follow</a>
            <div class="clear"></div>
        </div>
    </div>
</div>

<div id="" class="simple_email_block template hide data_block" data-contactFullName=''>
    <div class="contact_right_block">
        <div class="contact_right_block_content">
            <div class="contact_name">Invite a friend:</div>
            <div class="contact_input_div">
                <input class="join_input simple" placeholder="Your friend's email">
            </div>
            <div class="join_field_status">
                <img class="join_field_status_img" src="/images/input_ok.png">
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
{include file='components/js_common.tpl'}
<script>
    var emails_to_invite_count = {$emails_to_invite_count};
    var emails_counter = 1;
    var btn_disabled = false;

    $(document).ready(function() {
        $("#more_emails").click(function() {
            addEmails(3);
        });

        $(".used_email_remove").click(function() {
            var contact_id = $(this).attr('data-contactID');
            var dataBlock = $("#join_contact_"+contact_id);
            if (dataBlock.hasClass('simple_email_block')) {
                dataBlock.find(".join_input").val('');
                dataBlock.removeClass('active').attr("data-contactEmail", '');
                processRightBlock(dataBlock.find('.contact_right_block_content'));
                refreshUsedEmailsDiv();
            } else {
                dataBlock.find(".contact_left_block").click();
            }
        });

        $(".join_input").blur(function() {
            var dataBlock = $(this).parents('.data_block');
            if ($(this).hasClass('simple') && $(this).val()) {
                dataBlock.addClass('active');
            }
            dataBlock.attr('data-contactEmail', $(this).val());
            processRightBlock($(this).parents('.contact_right_block_content'));

            refreshUsedEmailsDiv();
        });

        $(".contact_left_block").click(function() {
            var dataBlock = $(this).parents('.data_block');
            if ($(this).hasClass("active")) {
                $(this).removeClass("active");
                dataBlock.removeClass('active');
                dataBlock.find(".contact_right_block_content").hide();
            } else {
                $(this).addClass("active");
                dataBlock.addClass('active');
                if (!$(this).hasClass('with_email')) {
                    var rightBlockContent = dataBlock.find(".contact_right_block_content");
                    rightBlockContent.show();
                    processRightBlock(rightBlockContent);
                }
            }
            refreshUsedEmailsDiv();
        });

        $("#join_invites_submit").click(function() {
            if (btn_disabled) {
                return false;
            }
            join_invites_submit();
            return false;
        });

        addEmails(emails_to_invite_count);
    });


    function setStatusImage(img, is_ok, alt, show) {
        if (is_ok) {
            img.attr('src', '/images/input_ok.png');
        } else {
            img.attr('src', '/images/input_error.png');
        }

        if (show) {
            img.show();
        } else {
            img.hide();
        }

        img.attr('title', alt);
    }

    function processRightBlock(block) {
        var email = $.trim(block.find(".join_input").val());
        var img = block.find(".join_field_status_img");
        if (email) {
            var parts = email.split('@');
            if (parts.length > 1 && parts[1]) {
                setStatusImage(img, true, '', true);
            } else {
                setStatusImage(img, false, 'You must enter a valid email address.', true);
            }
        } else {
            setStatusImage(img, true, '', false);
        }
    }

    function refreshUsedEmailsDiv() {
        $(".used_email_div:visible").remove();

        var emails = getEmailsData();
        var count = 0;
        var used_emails_section = $(".used_emails_section");
        for (var key in emails) {
            var dataBlock = $("#join_contact_" + key);
            var name = dataBlock.attr('data-contactFullName');
            var email = dataBlock.attr('data-contactEmail');
            var template = $(".used_email_div.template").clone(true);
            template.removeClass('template');
            template.find(".used_email_email").text(email);
            template.find(".used_email_name").text(name);
            template.find(".used_email_remove").attr('data-contactID', key);
            used_emails_section.append(template);
            template.show();
            count++;
        }
        if (count) {
            used_emails_section.show()
        } else {
            used_emails_section.hide()
        }

    }

    function addEmails(count) {
        for (var i = 0; i < count; i++) {
            var template = $(".simple_email_block.template").clone(true);
            template.attr('id', "join_contact_" + emails_counter);
            template.attr('data-contactID', emails_counter);
            $("#additional_emails").append(template).before($("<div>"));
            template.removeClass("template").show();
            emails_counter++;

            $('#join_contacts_div').animate({ scrollTop: $('#join_contacts_div').scrollTop()+300}, 'fast');
        }
    }

    function processErrors(errors) {
        if (errors) {
            $("#app_error_div").show();
        }

        $(".data_block.active .join_field_status_img").each(function() {
            setStatusImage($(this), true, '', true);
        });


        for (var key in errors) {
            var name = errors[key]['name'];
            var img = $("#join_contact_" + name).find(".join_field_status_img");
            setStatusImage(img, false, errors[key]['msg'], true);
        }
    }

    function getEmailsData() {
        var emails = { };
        $(".data_block.active").each(function() {
            emails[$(this).attr('data-contactID')] = $(this).attr('data-contactEmail');
        });
        return emails;
    }

    function join_invites_submit() {
        var entered_emails_count = $(".data_block.active").length;
        if (entered_emails_count > 3) {
            if (!confirm("Confirm you want to send " + entered_emails_count + " invites. Are you sure?")) {
                return false;
            }
        }

        var emails = getEmailsData();

        btn_disabled = true;
        $.ajax({
            data: {
                cmd: 'joinInvites',
                contact_emails: emails
            },
            success: function(data) {
                btn_disabled = false;
                if (data.status === 'success') {
                    if (data.errors) {
                        processErrors(data.errors);
                        return;
                    }
                    $(location).attr('href', data.redirect_url);
                } else {
                    alert(data.message);
                }
            }
        });
    }
</script>

{include file='components/footer_new.tpl'}