{include file='components/header_new.tpl'}

{* todo bear - made quickly, straight-forward. Should review later *}
<div class="profile_header_container">
    <div class="page_sizer_wide edit_profile_container">
        {* buttons *}
        <div class="edit_profile_header">
            <div class="you_are_aditing_div">You are editing this profile</div>
            <div class="fright">
                <div class="action_button profile_edit"><a href="{if !$new_vendor}{$vendor.my_url}{else}/{/if}">Cancel</a></div>
                <div class="action_button profile_edit_save" id="profile_save_button">Save Changes</div>
            </div>
            <div class="clear"></div>
        </div>
        {* edit fields *}
        <form class="" id="edit_profile">
            <input type="hidden" name="vendor_id" value="{if !$new_vendor}{$vendor.vendor_id}{/if}">
            <div class="fleft">
                <div class="profile_edit_field" id="edit_field_vendor_name">
                    <div class="edit_profile_field_title">Company Name:</div>
                    <input type="text" class="join_input" name="vendor_name" value="{if !$new_vendor}{$vendor.vendor_name|escape}{/if}">
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_location">
                    <div class="edit_profile_field_title">Location:</div>
                    <input type="text" class="join_input" name="location" value="{if !$new_vendor}{$vendor.location|escape}{/if}">
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_about">
                    <div class="edit_profile_field_title">
                        Byline:<br>
                        <span>(<span id="about_counter">{if !$new_vendor}{$vendor.about|escape|count_characters:true}{/if}</span>/{$max_about_length})</span>
                    </div>
                    <textarea class="join_input" name="about" id="about">{if !$new_vendor}{$vendor.about|escape}{/if}</textarea>
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_description">
                    <div class="edit_profile_field_title">Summary:</div>
                    <textarea class="join_input" name="description">{if !$new_vendor}{$vendor.description|escape}{/if}</textarea>
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_industries">
                    <div class="edit_profile_field_title">Industry:</div>
                    <select id="industries_select" name="industries[]" size="5">
                        {foreach from=$industry_tags item=data}
                            <option {if !$new_vendor && in_array($data.tag_id, $vendor.tag_list)}selected{/if} value="{$data.tag_id}">{$data.name}</option>
                        {/foreach}
                    </select>
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_company_size">
                    <div class="edit_profile_field_title">Company Size:</div>
                    <select id="company_size_select" name="company_size" size="5">
                        {foreach from=$tags_list_sizes item=data}
                            <option {if !$new_vendor && $vendor.company_size==$data}selected{/if} value="{$data}">{$data}</option>
                        {/foreach}
                    </select>
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_blocs">
                    <div class="edit_profile_field_title">Blocs:</div>
                    <select id="blocs_select" name="blocs[]" multiple="multiple" size="5">
                        {foreach from=$categories_structure item=category}
                            <optgroup label="{$category.name}">
                                {foreach from=$category.tags item=data}
                                    <option {if !$new_vendor && in_array($data.id, $vendor.tag_list)}selected{/if} value="{$data.id}">&nbsp;&nbsp;&nbsp;&nbsp;{$data.name}</option>
                                {/foreach}
                            </optgroup>
                        {/foreach}
                    </select>
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
            </div>
            <div class="fright edit_profile_right">
                <div class="profile_edit_field" id="edit_field_logo">
                    <div class="edit_profile_field_title">Company Logo:</div>
                    <div class="image_upload_div {if empty($vendor.logo_hash)}no_logo{/if}" style="background-image:url('{$vendor.logo.my_url_thumb}');">
                        <div class="image_upload_btn" id="vendor_logo_upload" data-entityType="vendor" data-entityID="{if !$new_vendor}{$vendor.vendor_id}{/if}"/></div>
                    </div>
                </div>
                <div class="profile_edit_field" id="edit_field_website">
                    <div class="edit_profile_field_title">Website:</div>
                    <input type="text" class="join_input" name="website" value="{if !$new_vendor}{$vendor.website}{/if}">
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_linkedin">
                    <div class="edit_profile_field_title">LinkedIn:</div>
                    <input type="text" class="join_input" name="linkedin" value="{if !$new_vendor}{$vendor.linkedin}{/if}">
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_facebook">
                    <div class="edit_profile_field_title">Facebook:</div>
                    <input type="text" class="join_input" name="facebook" value="{if !$new_vendor}{$vendor.facebook}{/if}">
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_twitter">
                    <div class="edit_profile_field_title">Twitter:</div>
                    <input type="text" class="join_input" name="twitter" value="{if !$new_vendor}{$vendor.twitter}{/if}">
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                <div class="profile_edit_field" id="edit_field_google_plus">
                    <div class="edit_profile_field_title">Google Plus:</div>
                    <input type="text" class="join_input" name="google_plus" value="{if !$new_vendor}{$vendor.google_plus}{/if}">
                    <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                </div>
                {if $autopost_allowed}
                    <div class="profile_edit_field" id="edit_field_autopost_tag_id">
                        <div class="edit_profile_field_title">
                            <input id="f_autopost_chk" type="checkbox" name="f_autopost" class="chk_valign" {if !$new_vendor && $vendor.f_autopost}checked{/if}>
                            Post from RSS
                        </div>
                        <select id="auto_bloc_select" name="autopost_tag_id" size="5" {if $new_vendor || !$vendor.autopost_tag_id}disabled{/if}>
                            {foreach from=$categories_structure item=category}
                                <option {if !$new_vendor && $category.id==$vendor.autopost_tag_id}selected{/if} value="{$category.id}">{$category.name}</option>
                            {/foreach}
                        </select>
                        <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                    </div>
                    <div class="profile_edit_field" id="edit_field_rss">
                        <div class="edit_profile_field_title">RSS:</div>
                        <input type="text" class="join_input" name="rss" value="{if !$new_vendor}{$vendor.rss}{/if}">
                        <div class="join_field_status"><img class="join_field_status_img" src="/images/input_ok.png"></div>
                    </div>
                {/if}
            </div>
            <div class="clear"></div>
        </form>
        <div id="profile_error_div" class="app_error">
            Sorry, check your errors.
        </div>
        <div id="profile_success_div" class="app_success">
            Your profile has been changed, redirecting...
        </div>
    </div>
</div>

{include file='components/js_common.tpl'}
<script src="/js/jquery.multiselect.js"></script>
<script>
    $(document).ready(function() {
        prepareImagesUpload();
        $("#about").addSymbolsCounter();

        $("#profile_save_button").click(function() {
            saveProfileData();
            return false;
        });

        $("#company_size_select, #industries_select, #auto_bloc_select").multiselect({
            header: false,
            multiple: false,
            minWidth: 265
        });
        $("#blocs_select").multiselect({
            header: false,
            minWidth: 265
        });

        $("#f_autopost_chk").click(function() {
            if (this.checked) {
                $("#auto_bloc_select").multiselect("enable");
            } else {
                $("#auto_bloc_select").multiselect("disable");
            }
        });
    });

    function processErrors(errors, form_id) {
        if (errors) {
            $("#account_app_error_div").show();
            $("#account_success_div").hide();
        }

        $("#" + form_id + " .join_field_status_img").attr('src', '/images/input_ok.png').attr('title', '').show();
        for (var key in errors) {
            var name = errors[key]['name'];
            $("#edit_field_" + name).find(".join_field_status_img")
                    .attr('src', '/images/input_error.png')
                    .attr('title', errors[key]['msg']);
        }
    }

    function saveProfileData() {
        $.ajax({
            url: '/cmd.php?cmd=saveVendorProfile',
            data: $("#edit_profile").serialize(),
            success: function(data) {
                if (data.status !== 'success') {
                    alert(data.message);
                    return;
                }
                if (data.errors) {
                    processErrors(data.errors, 'edit_profile');
                    return;
                } else {
                    $("#profile_error_div").hide();
                    $("#profile_success_div").show();
                    hideErrors('edit_profile');
                    if ($(location).attr('href') !== data.redirect_url) {
                        $(location).attr('href', data.redirect_url);
                    }
                }
            }
        });
    }

    function hideErrors(form_id) {
        $("#" + form_id + " .join_field_status_img").attr('src', '/images/input_ok.png').attr('title', '').hide();
    }

</script>

{include file='components/footer_new.tpl'}