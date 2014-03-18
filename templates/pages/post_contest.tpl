{include file='components/header_new.tpl' active='show_post'}
{include file='components/contest/votes_counter_block.tpl' for_show_post=true}

<div class="page_sizer_wide rails_container contest">
    <div class="left_rail">
        <form id="post_form">
            <input type="hidden" id="review_vendor_id" name="review_vendor_id" value="">
            <input type="hidden" id="review_vendor_rate" name="review_vendor_rate" value="">
            <div class="post_title_container">
                <div class="post_title_title">Nominate a Content Marketing Piece</div>
                <div class="post_title_text">
                    Please nominate only the highest quality links to our content marketing contest. While we don't forbid you from nominating your own post, we suggest you encourage a colleague to nominate your post.
                </div>
                <br><br>
                <div class="post_title_text">
                    The best posts will include detailed information in the post title, an accurate description of the content and include the author, if available.
                </div>
            </div>

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
                <input id="title_input" class="validate[required,maxSize[150]] post_field_title_input" type="text" name=""/>
                <div class="post_field_title author_field_title">
                    <label name="title">Author: </label>
                </div>
                <input id="author_name_input" class="post_field_title_input author author_input" type="text" name=""/>

                {if $is_admin}
                    <div class="post_author">
                        <div class="post_field_title author_field_title">
                            <label name="title">Posted by expert: </label>
                        </div>
                        <select id="force_user_id" class="post_field_user_input">
                            <option value="0">Choose an expert or leave empty</option>
                            {foreach from=$experts key=id item=user}
                                <option value="{$user.user_id}">{$user.first_name} {$user.last_name}</option>
                            {/foreach}
                        </select>
                    </div>
                {/if}
                <div class="post_tag_and_image_block">
                    <div class="fleft">
                        <div class="post_field_title">
                            <label name="title">Choose a category: </label>
                        </div>
                        <div class="post_tag_select_container">
                            <select id="category_input" class="validate[required] post_content_tag_select" size="7">
                                {foreach from=$contest_categories key=id item=tag}
                                    <option class="cat" value="{$id}">{$tag.tag_name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="post_contest_image_div fleft">
                        <div class="post_field_title">
                            <label name="title">Choose a picture:</label>
                        </div>
                        <div id="post_image" class="post_image_div" data-imageSrc=''></div>
                        <div class="post_image_selector">
                            <span id ="img_prev" class="post_image_selector_arrow hide">&lt;</span>
                            <span id="img_current" class="hide">0</span>
                            <span id="img_of_word">No images</span>
                            <span id="img_count" class="hide">0</span>
                            <span id ="img_next" class="post_image_selector_arrow hide">&gt;</span>
                        </div>
                        <input id="no_thumb_chk" class="post_no_thumb_chk" type="checkbox" name=""/>
                        <label class="no_thumb_text">No thumbnail?</label>
                    </div>
                    <div class="clear"></div>
                </div>

                <div class="post_text_container">
                    <div class="post_field_title">
                        <label name="title">Text (optional): </label>
                        <span class="symbols_count"><span id="text_input_counter">0</span>/500</span>
                    </div>
                    <textarea id="text_input" class="validate[maxSize[500]] post_field_text_input" name=""/></textarea>
                </div>
                <div class="clear"></div>
            </div>

            <div class="clear"></div>
            <div class="post_button_container">
                <div data-tweet='0' class="share_post_btn post_content_btn">Nominate</div>
                <div data-tweet='1' class="share_post_btn share_and_tweet_btn"><img class="tweet_img" src="/images/twitter.png">Nominate and Tweet</div>
            </div>
        </form>
    </div>
</div>
<div class="clear"></div>

{include file='components/contest/contest_nominate_and_join_popup.tpl'}

{include file='components/js_common.tpl'}
<script>
    var type = '{$type}';
    var tweet_after_post = null;

    $(document).ready(function() {
        $(".share_post_btn").click(function() {
            if ($("#post_form").validationEngine('validate')) {
                tweet_after_post = $(this).attr("data-tweet");
                if (is_logged) {
                    postContent(tweet_after_post);
                } else {
                    $("#popup_nominate_contest").show();
                }
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

        prepareNominationPopup();
        prepareContestVoting();

        $("#title_input").addSymbolsCounter();
        $("#text_input").addSymbolsCounter();
        setSelectedTag();
    });

    function setSelectedTag() {
        $("#category_input option:first").attr('selected', true);
        return false;
    }

    function postContent(tweet_after_post) {
        var data = { };
        data.post_type = type;
        data.title = $.trim($('#title_input').val());
        data.text = $.trim($('#text_input').val());
        data.author_name = $.trim($('#author_name_input').val());
        data.force_user_id = $.trim($('#force_user_id').val());
        data.f_anonym = 0;
        data.vendors = [];
        data.category = $('#category_input').val();
        data.url = $.trim($('#url_input').val());
        data.image = $('#post_image').attr('data-imageSrc');
        data.no_thumb = $('#no_thumb_chk').is(':checked') ? 1 : 0;
        data.tweet_after_post = tweet_after_post;

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
