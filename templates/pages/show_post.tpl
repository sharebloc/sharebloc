{include file='components/header_new.tpl' active='show_post'}
{if $use_contest_vote}
    {if $contest_id == 2}
        {include file='contest_marketo/votes_counter_block.tpl' for_nominations_page=true}
    {else}
        {include file='components/contest/votes_counter_block.tpl' for_nominations_page=true}
    {/if}
{/if}
<div class="page_sizer_wide rails_container">
    <div class="left_rail">
        <div class="showp_post_container">
            <div>
                {if $use_contest_vote}
                    {if $contest_id == 2}
                        {include file='components/front/front_post.tpl' post=$post_data for_show_post=true}
                    {else}
                        {include file='components/contest/contest_post.tpl' post=$post_data for_show_post=true}
                    {/if}
                    {include file='components/contest/contest_nominate_and_join_popup.tpl' f_post_comment=1}
                {else}
                    {include file='components/front/front_post.tpl' post=$post_data for_show_post=true}
                {/if}
            </div>
        </div>
        <div class="showp_order_container">
            <a class="post_type_link fleft {if $order=='rating'}active{/if}" href="{$post_data.my_url}&order=rating">Top</a>
            <a class="post_type_link fleft {if $order=='date'}active{/if}" href="{$post_data.my_url}&order=date">Recent</a>
            {if $is_admin}
                {if $post_data.post_type=='posted_link'}
                    <a id="change_logo_btn" class="post_type_link fleft">Change logo</a>
                {/if}
                {if $votes_data}
                    <a id="show_votes_btn" class="post_type_link fleft">Votes</a>
                {/if}
                <a id="show_stats_btn" class="post_type_link fleft">Stats</a>
            {/if}
            <div class="clear"></div>
            {if $is_admin && $post_data.post_type=='posted_link'}
                <div id="change_logo" class="change_post_logo hide">
                    <div class="image_upload_div {if !$post_data.logo_url_full}no_logo_for_post{/if}" style="background-image:url('{$post_data.logo_url_full}');">
                        <div class="image_upload_btn" id="image_upload" data-entityType="{$post_data.post_type}" data-entityID="{$post_data.post_id}"/></div>
                    </div>
                </div>
            {/if}
            {if $votes_data}
                <div id="vote_stats" class="post_stats hide">
                    <br>
                    <div>Sorted by date desc</div>
                    <br>
                    <table class="tracksTable">
                    {foreach $votes_data item=data}
                        <tr>
                            <td>{$data.user_id}</td>
                            <td>{$data.first_name} {$data.last_name}</td>
                            <td>{$data.email}</td>
                            <td>{$data.value}</td>
                            <td>{$data.vote_date}</td>
                        </tr>
                    {/foreach}
                    </table>
                </div>
            {/if}
            {if $is_admin}
                <div id="post_stats" class="tracks_table_div post_stats hide">
                    <div class="front_loader_div hide"><img src="/images/loading.gif"></div>
                </div>
            {/if}
        </div>
        <div class="showp_comments_container">
            {if $post_data.comment_list}
                <div id="comments_div">
                    {foreach $post_data.comment_list item=comment}
                        {include file='components/front/front_comment.tpl' comment=$comment post=$post_data}
                    {/foreach}
                </div>
            {else}
                No {$post_data.comments_title} yet.
            {/if}
        </div>
    </div>
    <div class="right_rail">
        <div class="right_rail_content">
            {if $show_share_links && !$use_contest_vote}
                {include file='components/front/invite_link.tpl' type='share_post'}
            {else if $use_contest_vote && $contest_id == 2}
                {include file='components/front/invite_link.tpl' type='share_post'}
            {/if}
        </div>
    </div>
    <div class="clear"></div>
</div>

<div class="comment_input_div hide">
    <div class="comment_textarea_div">
        <textarea class="comment_textarea"></textarea>
    </div>
    <div>
        <a class="small_btn cancel_link" href="#">Cancel</a>
        <div class="right_btns_section">
            {if $logged_in && $user_info.f_anonym_allowed && !$use_contest_vote}
                <label for="comment_anonym_chk">Submit anonymously?</label>
                <input class="anonym_chk" id="comment_anonym_chk" type="checkbox">
            {/if}
            <a class="small_btn comment_link" href="#">Comment</a>
        </div>
    </div>
</div>
{if $show_join_welcome_popup}
    {include file='components/welcome_popup.tpl'}
{/if}
{if !empty($reposted_popup_type)}
    {include file='components/front/repost_popup.tpl'}
{/if}
{include file='components/js_common.tpl'}
<script>
    var login_redirect_url = "{$login_redir_path}";
    var pressed_reply_button = null;
    var tweet_after_post = {$tweet_after_post};
    var post_type = "{$post_data.post_type}";
    var post_id = "{$post_data.post_id}";
    var loading = false;
    var contest_id = {$contest_id};

    $(document).ready(function() {
        $(".reply_btn").click(function() {
            if (!is_logged && !use_contest_vote) {
                window.location.href = login_redirect_url;
                return false;
            }
            hideReplyBlock();
            var entity_type = $(this).attr('data-entityType');
            if (entity_type === 'comment') {
                var block_for_new_comment = $(this).parents('.comment_main_div');
                var reply_to = block_for_new_comment.find(".comment_author_link").text();
            } else {
                var block_for_new_comment = $(this).parents('.post_main_div');
                var reply_to = '{$post_data.user.full_name|escape:'javascript'}';
            }
            move_reply_block(block_for_new_comment);

            $(".reply_btn").text('Reply');
            pressed_reply_button = $(this);
            pressed_reply_button.hide();
            cancel_the_text();
            add_reply_to_text(reply_to);
            return false;
        });

        $(".cancel_link").click(function() {
            hideReplyBlock();
            return false;
        });

        if (is_logged || use_contest_vote) {
            // show comment inputs by default
            $(".post_main_div .reply_btn").click();
        }

        $(".comment_link").click(function() {
            var comment_text = $.trim($('.comment_textarea').val());
            if (!comment_text) {
                alert("Comment text can not be empty");
                return false;
            }
            if (!is_logged && use_contest_vote) {
                $("#popup_nominate_contest").show();
                return false;
            }
            prepareAndPostComment();
            return false;
        });

        $("#welcome_popup_close").click(function(){
            $("#popup_welcome_container").hide();
            return false;
        });

        $("#repost_type_popup_close").click(function(){
            $("#popup_repost_container").hide();
            return false;
        });

        prepareContentDelete();
        prepareVoting();
        setRightRailFixed();

        prepareImagesUpload();

        if (use_contest_vote) {
            prepareContestVoting();
        }

        $("#change_logo_btn").click(function(){
            $("#change_logo").show();
            return false;
        });

        $("#show_votes_btn").click(function(){
            $("#vote_stats").show();
            return false;
        });

        $("#show_stats_btn").click(function(){
            $("#post_stats").show();
            getPostStats('postStats');
            return false;
        });

        prepareCopyToClipboard();
        prepareSharing();
        prepareRepost();

        $("#submit_votes_send").click(function() {
            // for contest
            submitVotes();
            return false;
        });

        if (tweet_after_post) {
            $("#tweet_type_popup_close").click(function() {
                $("#popup_tweet_type_selection").hide();
                return false;
            });

            $("#popup_tweet_type_selection .share_post_btn").click(function() {
                $("#popup_tweet_type_selection").hide();
                return true;
            });

            if (post_type==='posted_link') {
                $("#popup_tweet_type_selection").show();
            } else {
                $("#tweet_btn").click();
            }
        }

        $("#popup_repost_container .share_post_btn").click(function() {
            $("#popup_repost_container").hide();
            return true;
        });

    });

    function prepareAndPostComment() {
        var data = { };
        data.comment_id = '';
        data.post_type = post_type;
        data.post_id = post_id;
        data.comment_text = $.trim($('.comment_textarea').val());
        data.privacy = $('#comment_anonym_chk').is(':checked') ? 'anonymous' : 'public';
        sendComment(data);
        return false;
    }

    function hideReplyBlock() {
        cancel_the_text();
        if (pressed_reply_button) {
            pressed_reply_button.show();
            pressed_reply_button = null;
        }
        $(".comment_input_div").hide();
        $(".reply_btn").text('Reply');
    }

    function move_reply_block(div_to_append) {
        div_to_append.append($(".comment_input_div").detach());
        $(".comment_input_div").show();
    }

    function add_reply_to_text(reply_to) {
        var old_text = $(".comment_textarea").val();
        $(".comment_textarea").val("@" + reply_to + ': ' + old_text);

    }

    function cancel_the_text() {
        $(".comment_textarea").val('');
    }

    function sendComment(data) {
        $.ajax({
            data: {
                cmd: 'postComment',
                data: data
            },
            success: function(data) {
                if (data.status === 'success') {
                    if (is_logged) {
                        location.reload();
                    } else {
                        var url = "{$join_redir_path}";
                        $(location).attr('href', url);
                    }
                } else {
                    alert(data.message);
                }
            }
        });
    }

    function getPostStats() {
        if (loading) {
            return false;
        }
        loading = true;

        $(".tracksTable").remove();
        $(".front_loader_div").show();

        $.ajax({
            data: {
                cmd: 'getPostStats',
                post_id: post_id,
                post_type: post_type,
            },
            success: function(data) {
                loading = false;
                $(".front_loader_div").hide();
                if (data.status === 'success') {
                    updateStats(data.data);
                } else {
                    alert(data.message);
                }
            }
        });
    }

    function updateStats(data) {
        var tables = $(data['html']);
        $(".tracks_table_div").append(tables);
    }
</script>

{include file='components/footer_new.tpl'}