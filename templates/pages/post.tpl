{include file='components/header_new.tpl' active='show_post'}

<div class="page_sizer_wide rails_container">
    <div class="left_rail">
        <form id="post_form">
            <div class="post_title_container">
                <div class="post_title_title">Post to ShareBloc</div>
                <div class="post_title_text">
                    {if $type == 'posted_link'}
                        You are posting a link. Try using a descriptive title to get better results. Be sure to add tags to your link and connect it with any companies mentioned.
                    {elseif $type == 'question'}
                        You are asking a question. Ask your question in the title and expand on it on the text section. Be sure to add tags to your question and connect it with any companies mentioned.
                    {/if}
                </div>
            </div>

            <div class="post_type_container">
                <a class="post_type_link fleft {if $type == 'posted_link'}active{/if}" href="/post/link">Link</a>
                <a class="post_type_link fleft {if $type == 'question'}active{/if}" href="/post/question">Question</a>
                <div class="clear"></div>
            </div>
            {if $type == 'posted_link'}
                <div class="post_field_container">
                    <div class="post_field_title">
                        <label name="title">URL: </label>
                        <img id="loader" class="post_loader_img fright hide" src="/images/loading.gif"></span>
                    </div>
                    <input id="url_input" class="validate[required] post_field_link_input" type="text" name=""/>
                </div>
            {else}
                <div class="post_field_container">
                    <div class="post_field_title">
                        <label name="title">Title: </label>
                        <span class="symbols_count"><span id="title_input_counter">0</span>/150</span>
                    </div>
                    <input id="title_input" class="mentioned_field validate[required,maxSize[150]] post_field_title_input" type="text" name=""/>
                </div>
            {/if}
            {if $type == 'posted_link'}
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
            {elseif $type == 'question'}
                <div class="post_field_container">
                    <div class="post_field_title">
                        <label name="title">Text (optional): </label>
                        <span class="symbols_count"><span id="text_input_counter">0</span>/500</span>
                    </div>
                    <textarea id="text_input" class="mentioned_field validate[maxSize[500]] post_field_text_input" name=""/></textarea>
                    <div class="clear"></div>
                </div>
            {/if}

            <div class="post_field_container">
                <div class="post_tag_block fleft">
                    <div class="post_field_title">
                        <label name="title">Choose a main tag: </label>
                    </div>
                    <div class="post_tag_select_container">
                        <select id="category_input" class="validate[required] post_field_tag_select" size="7">
                            {foreach from=$categories key=id item=tag}
                                <option class="cat" value="{$subcategories.$id.tag_id}">{$subcategories.$id.tag_name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="post_tag_block fright">
                    <div class="post_field_title">
                        <label name="title">Choose a sub tag (optional): </label>
                    </div>
                    <div class="post_tag_select_container">
                        <select id="subcategory_input" class="post_field_tag_select" size="7">
                        </select>
                    </div>
                </div>
                <div class="clear"></div>
            </div>

            <div class="post_field_container">
                <div class="post_field_title">
                    <label name="title">Add a company tag (optional):</label>
                </div>
                <input id="vendor_input" class="mentioned_field post_field_vendor_input" type="text" name="" placeholder="Add a company"/>
                <div id="mentions_div" class="mentioned_vendor fleft">
                </div>
                <div class="clear"></div>
            </div>

            <div class="clear"></div>
            <div class="post_button_container">
                <a class="share_post_btn post_content_btn" href="#" data-tweet='0'>{if $logged_in}Post{else}Post and join{/if}</a>
                <div data-tweet='1' class="share_post_btn share_and_tweet_btn"><img class="tweet_img" src="/images/twitter.png">Post and Tweet</div>
                {if $logged_in && $user_info.f_anonym_allowed}
                    <div class="post_anonym_div">
                        <input id="f_anonym_chk" class="post_anonym_chk" type="checkbox" name=""/>
                        <span class="post_anonym_text ">Post anonymously?</span>
                    </div>
                {/if}
            </div>
        </form>
    </div>
</div>
<div class="clear"></div>
<div class="mentioned_tag template hide">
    <div class="mentioned_tag_title"></div>
    <div class="mentioned_tag_x mention_delete" href="javascript:void(0)">x</div>
</div>

{include file='components/js_common.tpl'}
<script>
    var categories = {$categories_str}
    var subcategories = {$subcategories_str};
    var mentions = {};
    var mention_count = 0;
    var type = '{$type}';
    var refer_vendor = {if !empty($refer_vendor)}{$refer_vendor}{else}null{/if};
    var selected_tag_id = {if !empty($selected_tag_id)}{$selected_tag_id}{else}null{/if};
    var tweet_after_post = null;

    $(document).ready(function() {
        $(".share_post_btn").click(function() {
            if ($("#post_form").validationEngine('validate')) {
                tweet_after_post = $(this).attr("data-tweet");
                postContent(tweet_after_post);
            }
            $("#post_form").validationEngine({
            });
            return false;
        });
        $("#url_input").blur(function() {
            getUrlData($(this).val());
        });
        $("#img_next").click(function() {
            rotateImg(true);
        });
        $("#img_prev").click(function() {
            rotateImg(false);
        });
        $("#title_input").addSymbolsCounter();
        $("#text_input").addSymbolsCounter();
        $("#reason_text_input").addSymbolsCounter();
        $("#advice_text_input").addSymbolsCounter();
        addReferVendor();

        $("#category_input").change(function() {
            var selected_cat_id = $(this).val();
            if (selected_cat_id) {
                $("#subcategory_input option").remove();
                var parents = categories[selected_cat_id];
                for (var i = 0; i < parents.length; i++) {
                    if (parents[i] === selected_cat_id) {
                        continue;
                    }
                    var new_option = $("<option>");
                    new_option.addClass('subcat');
                    new_option.val(subcategories[parents[i]].tag_id);
                    new_option.text(subcategories[parents[i]].tag_name);

                    $("#subcategory_input").append(new_option);
                }
            }
        });

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
            $(this).parents('.mentioned_tag').remove();
            mentions[$vendor_id] = 0;
            mention_count--;
        });

        setSelectedTag();
        $("#category_input").change();
    });

    function addReferVendor() {
        if (!refer_vendor) {
            return false;
        }
        addMentionedVendor(refer_vendor);
    }

    function setSelectedTag() {
        if (!selected_tag_id) {
            $("#category_input option:first").attr('selected', true);
        } else {
            $("#category_input option[value='"+selected_tag_id+"']").attr("selected", true);
        }

        return false;
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

    function getVendors() {
        var vendors = [];
        for (var key in mentions) {
            if (mentions[key]) {
                vendors.push(key);
            }
        }
        return vendors;
    }

    function postContent() {
        var data = { };
        data.post_type = type;
        data.title = $.trim($('#title_input').val());
        data.text = $.trim($('#text_input').val());
        data.f_anonym = $('#f_anonym_chk').is(':checked') ? 1 : 0;
        data.tweet_after_post = tweet_after_post;

        data.vendors = getVendors();
        data.category = $('#category_input').val();
        if ($('#subcategory_input').val()) {
            data.subcategory = $('#subcategory_input').val();
        }

        if (type === 'posted_link') {
            data.url = $.trim($('#url_input').val());
            data.image = $('#post_image').attr('src');
            data.no_thumb = $('#no_thumb_chk').is(':checked') ? 1 : 0;
        }

        sendPostContent(data);
    }

    function sendPostContent(data) {
        data.cmd = 'postContent';
        $.ajax({
            data: data,
            success: function(data) {
                if (data.status === 'success') {
                    var url = "{$join_redir_path}";
                    if (is_logged) {
                        url = data['result_url'];
                    }
                    $(location).attr('href', url);
                } else {
                    alert(data.message);
                }
            }
        });
    }
</script>

{include file='components/footer_new.tpl'}
