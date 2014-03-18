var show_user_menu = false;
var inputs_locked = false;

/* Common functions */
function isSBCookieSet(name) {
    var isset = (document.cookie.indexOf('sb.'+name)!==-1)? true : false;
    return isset;
}

function setSBCookie(name, value) {
    name = 'sb.'+name;
    expire_date = new Date();
    expire_date.setFullYear(expire_date.getFullYear()+1);
    document.cookie = name+"="+escape(value)+"; path=/; expires="+expire_date.toUTCString();
}

function getSBCookie(name) {
    var cookie = "" + document.cookie;
    if (!cookie) {
        return null;
    }
    var search = 'sb.'+name+"=";
    var offset = cookie.indexOf(search);
    if (offset!==-1) {
        offset += search.length;
        end = cookie.indexOf(";", offset);
        if (end === -1) {
            end = cookie.length;
        }
        return unescape(cookie.substring(offset, end));
    }
    return null;
}

$.ajaxSetup({
  url: '/cmd.php',
  type: 'POST',
  dataType: 'json',
  timeout: ajax_timeout,
  error: function(jqXHR, textStatus, errorThrown){showAjaxError('Request to server failed: '+textStatus+" (" + errorThrown+")")}
});

function showAjaxError(message) {
    stop_blinking = true;
    if (blinking) {
        setTimeout(function(){showAjaxError(message);}, 250);
        return false;
    }
    lockInputs(false);
    message = message.replace("\\n", "\n");
    alert(message);
    return false;
}

$(document).ready(function() {
    $(document).on("click", ".trackedOutboundLink, .trackedOutboundDiv a", function(event) {
        $(this).attr('target', '_blank');
        $.ajax({
            data: {cmd: 'track_link', type: 'outbound', dest: $(this).attr('href')}
        });
    });

    $("#username_link, #user_menu").hover(function() {
        showUserMenu(true);
    }, function() {
        showUserMenu(false);
    });

    $("#notifications_circle").mouseover(function() {
        $("#header_notifications_div").show();
        $(".notifications_circle_div").addClass('active');

    });

    $("#header_notifications_div").mouseleave(function() {
        $("#header_notifications_div").hide();
        $(".notifications_circle_div").removeClass('active');
    });

    $("#close_status").click(function() {
        $("#status_box").hide();
    });

    $("#invites_link").click(function() {
        showInvitesPopup();
        return false;
    });

    $("#invite_front_popup_close").click(function() {
        $("#popup_invite_front_container").hide();
        return false;
    });

    $("#invite_front_send").click(function() {
        sendFrontInvite();
        return false;
    });

    if (alert_message) {
        alert(alert_message);
    }

    // search currently disabled
    // bindAutocompleteToMenuSearch();

    if ($(".right_rail").length) {
        correctLeftRailHeight();
    }
});

function showInvitesPopup() {
    $("#invite_front_popup_close").text("Cancel");
    $("#front_invite_success").hide();

    $("#invite_front_inputs, #invite_front_send").show();

    $("#popup_invite_front_container").show();
    return false;
}

/* Used as right rail is fixed and when it's smaller than left one "terms provacy" block is on a wrong place */
function correctLeftRailHeight() {
    var height_diff = $(".right_rail").height() - $(".left_rail").height();
    if (height_diff > 0) {
        $(".left_rail").css('margin-bottom', height_diff);
    } else {
        $(".left_rail").css('margin-bottom', 0);
    }
}

function lockInputs(lock) {
    inputs_locked = lock;
}
function isLocked() {
    return inputs_locked;
}

function showUserMenu(show) {
    show_user_menu = show;
    setTimeout(function() {
        toggleUserMenu();
    }, 100);
}

function toggleUserMenu() {
    if (show_user_menu) {
        $("#user_menu").show();
        $("#username_link").addClass('header_person_hover');
    } else {
        $("#user_menu").hide();
        $("#username_link").removeClass('header_person_hover');
    }
}

function setRightRailFixed(shift) {
    shift = shift || 0;
    var right_rail_top = $('.right_rail_content').offset().top;
    $(window).scroll(function() {
        if ($(this).scrollTop() + shift >= right_rail_top) {
            $('.right_rail_content').addClass('at_top');
        } else {
            $('.right_rail_content').removeClass('at_top');
        }
    });
}

/* FUNCTIONS BELOW ARE FOR FRONTPAGE, USER, COMPANY AND BLOC PAGES */

/* Content filter related functions */
function prepareContentFilters() {
    if (!is_logged) {
        $("#my_feed_link").click(function() {
            showTagsFilterPopup();
            return false;
        });
    }

    $("#edit_feed_lnk").click(function() {
        showTagsFilterPopup();
        return false;
    });
    $(".mention_delete").click(function() {
        $("#remove_tag_from_filter").val($(this).attr('data-tagID'));
        $("#tags_filter_form").submit();
        return false;
    });

    $("#filter_save").click(function() {
        if (!is_logged) {
            return true;
        }
        $("#tags_filter_form").submit();
        return false;
    });

    $("#filter_clear").click(function() {
        $(".category_chk").attr('checked', false);
        return false;
    });

    $("#filter_cancel").click(function() {
        $(".category_chk").attr('checked', false);
        $("#filter_popup_container").hide();
        return false;
    });
}

function showTagsFilterPopup() {
    for (var i = 0; i < tags_filter.length; i++) {
        $("#category_chk_" + tags_filter[i]).attr('checked', true);
    }
    $("#filter_popup_container").show();
}

/* Functions to delete content */
function prepareContentDelete() {
    $(document).on("click", ".delete_btn", function(event) {
        if (!confirm("Confirm you want to delete. Are you sure?")) {
            return false;
        }
        deletePost($(this));
    });
}

function prepareRepost() {
    $(document).on("click", ".repost_link", function(event) {
        repost($(this));
        return false;
    });
}

/* todo merge with show post function */
function deletePost(delete_btn) {

    var entity_type = $(delete_btn).attr('data-entityType');
    if (entity_type === 'comment') {
        var post_container = $(delete_btn).parents(".comment_container");
        var type = entity_type;
        var id = post_container.attr('data-commentId');
    } else {
        var post_container = $(delete_btn).parents(".post_container");
        var type = post_container.attr('data-postType');
        var id = post_container.attr('data-postId');
    }

    var id_param_name = type + '_id';

    var data = {};
    data.cmd = 'delete_' + type;
    data[id_param_name] = id;

    $.ajax({
        data: data,
        success: function(data) {
            if (data.status === 'success') {
                post_container.remove();
            } else {
                alert(data.message);
            }
        }
    });
}
function prepareFollowing(no_refresh) {
    no_refresh = no_refresh || true;
    correctFollowsBlockMargin();
    $(document).on("click", ".profile_follow", function() {
        follow($(this), no_refresh);
        return false;
    });
    $("#show_followers").click(function() {
        showFollowers();
        return false;
    });
    $("#show_following").click(function() {
        showFollowing();
        return false;
    });
    $("#show_recent_connections").click(function() {
        showRecentConnections();
        return false;
    });
    $(".show_follow_type").click(function() {
        showFollowType($(this));
        return false;
    });
    $(".follow_all_chk").click(function() {
        batchFollow($(this));
    });
}
function prepareFollowIconsSwitch() {
    $("#following_link").click(function() {
        $(this).addClass('active');
        $("#followers_link").removeClass('active');
        $(".profile_summary.following").show();
        $(".profile_summary.followers").hide();
        return false;
    });

    $("#followers_link").click(function() {
        $(this).addClass('active');
        $("#following_link").removeClass('active');
        $(".profile_summary.following").hide();
        $(".profile_summary.followers").show();
        return false;
    });
}

function prepareImagesUpload() {
    $(".image_upload_btn").each(function() {
        var button = $(this);
        var entity_type = button.attr('data-entityType');
        var entity_id = button.attr('data-entityID');
        button.uploadify({
            'uploader': '/uploadify/uploadify.swf',
            'script': '/uploadify/uploadify.php?uploadifysess=' + session_id,
            'cancelImg': '/uploadify/cancel.png',
            'auto': true,
            'multi': false,
            'hideButton': true,
            'width': 100,
            'height': 100,
            'wmode': 'transparent',
            'fileExt': '*.jpg;*.gif;*.png',
            'scriptData': {
                'entity_type': entity_type, 'entity_id': entity_id
            },
            'onComplete': function(event, queueID, fileObj, response) {
                processImageUpload(response, button);
                return false;
            }
        });
    });
}

function processImageUpload(response, button) {
    var data = JSON.parse(response);
    if (data.err_msg) {
        alert(data.err_msg);
        return false;
    }

    var img_src = data.my_url + '?time=' + new Date().getTime();
    var style = "url('" + img_src + "')";
    button.parents('.image_upload_div').css('background-image', style);
    return false;
}

/* todo replace next 3 functions with one */
function showFollowers() {
    $("#following_div").hide();
    $("#recent_connections_div").hide();
    $(".posts_filters_div").hide();
    $("#followers_div").show();
    correctFollowsBlockMargin();
    $("#show_following").removeClass('active');
    $("#show_recent_connections").removeClass('active');
    $("#show_followers").addClass('active');
    correctLeftRailHeight();
}

function showFollowing() {
    $("#followers_div").hide();
    $("#recent_connections_div").hide();
    $(".posts_filters_div").show();
    $("#following_div").show();
    correctFollowsBlockMargin();
    $("#show_followers").removeClass('active');
    $("#show_recent_connections").removeClass('active');
    $("#show_following").addClass('active');
    correctLeftRailHeight();
}

function showRecentConnections() {
    $("#followers_div").hide();
    $("#following_div").hide();
    $(".posts_filters_div").hide();
    $("#recent_connections_div").show();
    correctFollowsBlockMargin();
    $("#show_followers").removeClass('active');
    $("#show_following").removeClass('active');
    $("#show_recent_connections").addClass('active');
    correctLeftRailHeight();
}

/* todo temp solution */
function correctFollowsBlockMargin() {
    $(".follow_block").removeClass('even');
    $(".follow_block:visible").each(function(index, block){
        if (index%2===1){
            $(block).addClass('even');
        }
    });
}

function showFollowType(link) {
    var type = link.attr('data-showFollowType');
    if (type==='all') {
        $(".follow_block").show();
    } else {
        $(".follow_block").hide();
        $(".follow_entity_"+type).show();
    }

    $(".show_follow_type").removeClass('filter_active');
    link.addClass('filter_active');
    correctFollowsBlockMargin();
    correctLeftRailHeight();
}

function follow(follow_data_element, no_refresh) {
    var followed = follow_data_element.hasClass('active') ? 1 : 0;
    var whom_type = follow_data_element.attr('data-whomType');
    var whom_id = follow_data_element.attr('data-whomId');
    var who_uid = follow_data_element.attr('data-uid');

    $.ajax({
        data: {
            cmd: 'setFollow',
            follow_data: {
                whom_type: whom_type,
                whom_id: whom_id,
                followed: followed
            }
        },
        success: function(data) {
            if (data.status === 'success') {
                if (followed) {
                    follow_data_element.text('Follow');
                    follow_data_element.removeClass('active');
                    var curr_count = parseInt($("#followers_count").attr('data-followersCount'));
                    curr_count--;
                    $("#followers_count").text(curr_count);
                    $("#followers_count").attr('data-followersCount', curr_count);
                    // todo fix later - this will delete both follow/following elements with the same uid. not urgent.
                    $(".follow_block[data-followerUid="+who_uid+"]").remove();
                    $(".follow_logo_small[data-followerUid="+who_uid+"]").parent().remove();
                } else {
                    if (no_refresh){
                        follow_data_element.text('Following');
                        follow_data_element.addClass('active');
                    } else {
                        location.reload();
                    }
                }
                if (typeof(setNextBtnActivity) === 'function') {
                    setNextBtnActivity();
                }
            } else if (data.status === 'failure') {
                alert(data.message);
            }
        }
    });
}

function batchFollow(follow_all_chk) {
    var followed = follow_all_chk.is(":checked") ? 0 : 1;
    var whom_type = follow_all_chk.attr('data-whomType');
    var whom_id = [];
    $(".profile_follow:visible").each(function(){
        whom_id.push($(this).attr('data-whomId'));
    });

    $.ajax({
        data: {
            cmd: 'setFollow',
            follow_data: {
                whom_type: whom_type,
                whom_id: whom_id,
                followed: followed,
                f_batch: true
            }
        },
        success: function(data) {
            if (data.status === 'success') {
                if (followed) {
                    $(".profile_follow:visible").text('Follow').removeClass('active');
                } else {
                    $(".profile_follow:visible").text('Following').addClass('active');
                }
            } else if (data.status === 'failure') {
                alert(data.message);
            }
        }
    });
}
function prepareVoting() {
    $(document).on("click touchstart", ".arrow_up, .arrow_down", function() {
        if (!is_logged) {
            window.location.href = login_url;
            return false;
        }
        vote($(this));
    });
}

function prepareNominationPopup() {
    $("#nominate_contest_popup_close").click(function() {
        $("#popup_nominate_contest").hide();
    });

    $("#nominate_contest_join").click(function() {
        var entity_type = $(this).attr("data-entityType");
        if (entity_type === 'comment') {
            prepareAndPostComment();
        } else {
            postContent(tweet_after_post);
        }
    });
}

function prepareContestVoting() {
    $(".votes_submit_link").click(function() {
        $("#popup_submit_votes_container").show();
        return false;
    });
    $("#popup_submit_votes_container .cancel_btn").click(function() {
        $("#popup_submit_votes_container").hide();
        updateUserFSponsorEmail();
        $("#submit_votes_success #get_sponsor_email_chk_thanks").attr('checked', 'checked');
        return false;
    });
    $("#submit_votes_send").click(function() {
        submitVotes();
        return false;
    });
}

function prepareSharing() {
    $(document).on("click", ".share_post_btn", function(event) {
        var provider = $(this).attr('data-provider');
        var additional_text = $(this).attr('data-text');
        var link = $(this).attr('data-shareUrl');
        if (!link) {
            link = '';
        }

        if (provider === 'mail') {
            showMailSharePopup(additional_text, link);
        } else {
            if ($(this).parents("#submit_votes_success").length) {
                $("#submit_votes_success .submit_votes_close_btn").click();
            }
            if ($(this).parents("#popup_contest_rules_container").length) {
                $("#popup_contest_rules_container #close_popup_rules").click();
            }
            openSharePopup(link, provider, additional_text);
        }

        $.ajax({
            data: {cmd: 'track_link', type: 'share', dest: provider}
        });
        return false;
    });

    $("#share_link_popup_close").click(function() {
        $("#popup_share_link_container").hide();
        return false;
    });

    $("#share_link_send").click(function() {
        sendShareLink();
        return false;
    });
}

function prepareCopyToClipboard() {
    ZeroClipboard.setDefaults( { moviePath: '/js/ZeroClipboard.swf' } );
    $(".copy_btn").each(function() {
        var clip = new ZeroClipboard($(this));
        clip.setHandCursor(true);

        clip.on( 'complete', function (client, args) {
            $("#"+this.id).text('Copied!');
        });
    });
}

function vote(arrow) {
    var inactive = arrow.css('cursor')!=='pointer';
    if ((use_contest_vote && contest_id === 1) ||
            (inactive && !is_admin)) {
        return false;
    }

    var vote_value = parseInt(arrow.attr('data-voteValue'));
    var entity_type = arrow.attr('data-entityType');

    if (entity_type === 'comment') {
        var type = entity_type;
        var id = arrow.attr('data-commentId');
    } else {
        var type = arrow.attr('data-postType');
        var id = arrow.attr('data-postId');
    }

    $.ajax({
        data: {
            cmd: 'vote',
            use_contest_vote: use_contest_vote,
            vote: {
                type: type,
                entity_id: id,
                vote_value: vote_value
            }
        },
        success: function(data) {
            if (data.status === 'success') {
                if (!data.vote_data.no_votes_left_error) {
                    updateVote(data.vote_data, id, type);
                }
                if (use_contest_vote) {
                    processContestVote(data.votes_left, data.vote_data.no_votes_left_error);
                }
            } else if (data.status === 'failure') {
                alert(data.message);
            }
        }
    });
}

function updateVote(data, id, type) {
    $("#" + type + "_vote_total_" + id).text(data.total);

    var vote_block = $("#vote_" + type + "_" + id);
    var arrow_up = vote_block.find(".arrow_up");
    var arrow_down = vote_block.find(".arrow_down");

    arrow_up.removeClass('arrow_active arrow_inactive');
    arrow_down.removeClass('arrow_active arrow_inactive');

    if (data.user_vote < 0) {
        arrow_up.addClass("arrow_active");
        arrow_down.addClass("arrow_inactive");
    } else if (data.user_vote > 0) {
        arrow_up.addClass("arrow_inactive");
        arrow_down.addClass("arrow_active");
    } else {
        arrow_up.addClass("arrow_active");
        arrow_down.addClass("arrow_active");
    }

    if (data.total < 1) {
        arrow_down.hide();
    } else {
        arrow_down.show();
    }
}

/* contest-related functions */
function processContestVote(votes_left, no_votes_left_error) {
    if (no_votes_left_error) {
        alert('No votes remaining today');
        return false;
    }

    var suffix = (votes_left===1) ? ' vote' : ' votes';
    $(".votes_left_count_number").text(votes_left + suffix);
    if (votes_left < 1) {
        $(".votes_counter_div").hide();
        $(".votes_submit_div").show();
        if (is_logged || is_voter) {
            $("#popup_submit_votes_container").show();
        }

    } else {
        $(".votes_counter_div").show();
        $(".votes_submit_div").hide();
    }
}

function submitVotes() {
    $.ajax({
        data: {
            cmd: 'submitContestVotes',
            first_name: $("#submit_votes_first_name").val(),
            last_name: $("#submit_votes_last_name").val(),
            email: $("#submit_votes_email").val()
        },
        success: function(data) {
            if (data.status === 'success') {
                if (data.errors) {
                    processErrors(data.errors);
                    if (data.email_exists) {
                        alert("You have already signed up before. Please sign in.");
                    }
                    return;
                }
                $("#popup_submit_votes_container .popup_function, #submit_votes_form_div").hide();
                $("#submit_votes_success").show();
            } else if (data.status === 'failure') {
                alert(data.message);
            }
        }
    });
    return false;
}

/* end of contest-related functions */

/* More-related functions */
function preparePageForMore() {
    window.loading_more = false;
    $(window).scroll(function() {
        if ($(window).scrollTop() < $(document).height() - 2 * $(window).height()) {
            return true;
        }
        getMoreEntities();
    });
}

function getActiveMoreContainer() {
    var more_container = $(".more_container:visible:first");
    if (!more_container.length) {
        return false;
    }

    if (parseInt(more_container.attr('data-loadingMore')) || parseInt(more_container.attr('data-noMore'))) {
        return false;
    }
    return more_container;
}

function addEntities(html_divs, more_container) {
    for (var i = 0; i < html_divs.length; i++) {
        var html_div = $(html_divs[i]);
        more_container.append(html_div);
    }
}

function getMoreEntities() {
    var more_container = getActiveMoreContainer();
    if (!more_container) {
        return true;
    }

    var page_type = more_container.attr('data-pageType');
    var entity_id = more_container.attr('data-entityID');
    var offset_for_next_query = more_container.attr('data-offsetForMore');
    var follow_type = more_container.attr('data-followType') || 0;

    more_container.attr('data-loadingMore', 1);
    more_container.siblings(".front_loader_div").show();
    $.ajax({
        data: {
            cmd: 'getMoreEntities',
            offset: offset_for_next_query,
            page_type: page_type,
            entity_id: entity_id,
            follow_type: follow_type
        },
        success: function(data) {
            if (data.status === 'success') {
                addEntities(data.data.html_divs, more_container);
                more_container.attr('data-offsetForMore', data.data.offset_for_next_query);
                more_container.attr('data-noMore', data.data.no_more_content);
                more_container.attr('data-loadingMore', 0);
                more_container.siblings(".front_loader_div").hide();

                if (follow_type) {
                    correctFollowsBlockMargin();
                    correctLeftRailHeight();
                }

            } else {
                alert(data.message);
            }
        }
    });
}

/* FUNCTIONS ABOVE ARE FOR FRONTPAGE, USER, COMPANY AND BLOC PAGES */

/* Invites */
function sendFrontInvite() {
    $("#front_invite_form").validationEngine({scroll: false});
    if (!$("#front_invite_form").validationEngine('validate')) {
        return false;
    }

    var data = $("#front_invite_form").serialize();

    $.ajax({
        url: '/cmd.php?cmd=sendCustomInvite',
        data: data,
        success: function(data) {
            if (data.status === 'success') {
                $("#front_invite_success").show();
                $("#invite_front_inputs, #invite_front_send").hide();
                $("#invite_front_popup_close").text("Close");
                $(".popup_input_row input").val("");
            } else {
                alert(data.message);
            }
        }
    });

    $("#hide_browse_popup").click();
    there_are_changes = true;
    return false;
}

var blinking = false;
var stop_blinking =false;
// WARN: clears inline style after blinking ends
function blink(j_elem, wait) {
    if (wait) {
        stop_blinking = false;
        setTimeout(function(){blink(j_elem, false);}, 400);
        return;
    }

    if (stop_blinking) {
        j_elem.attr({"style": ''});
        stop_blinking = false;
        blinking = false;
        return;
    }
    blinking = true;
    j_elem.animate({"opacity": 0.2}, 500).animate({"opacity": 1},800, function(){blink(j_elem, false);});
}

function bindAutocompleteToMenuSearch() {
    $("#search_name").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "/autocomplete.php",
                dataType: "jsonp",
                data: {
                    featureClass: "search[name]",
                    style: "full",
                    maxRows: 12,
                    name_startsWith: request.term
                },
                success: function(data) {
                    response(
                            $.map(data.results, function(item) {
                        return {
                            label: item.Name,
                            value: item.Name,
                            code: item.Code,
                            type: item.Type
                        }
                    }));
                }
            });
        },
        minLength: 2,
        select: function(event, ui)
        {
            $("#search_code").val(ui.item.code);
            $("#search_type").val(ui.item.type);
            $('#search_submit').submit();
        }
    });
}

(function($) {
    $.fn.addSymbolsCounter = function(max_length) {
        function updateTitleCounter(input, counter, max_length) {
            var length = input.val().length;
            if (max_length && (length > max_length)) {
                var trimmed_value = input.val().slice(0, max_length);
                input.val(trimmed_value);
                length = input.val().length;
            }
            counter.html(length);
        }
        if (max_length === undefined) {
            max_length = false;
        }
        this.each(function() {
            var my_id = $(this).attr('id');
            if (!my_id) {
                return false;
            }

            var my_counter_el = $("#" + my_id + "_counter");
            if (!my_counter_el.length) {
                return false;
            }

            var me = $(this);
            $(this).keydown(function() {
                setTimeout(function() {
                    updateTitleCounter(me, my_counter_el, max_length)
                }, 10);
                return true;
            });
            $(this).keyup(function() {
                setTimeout(function() {
                    updateTitleCounter(me, my_counter_el, max_length)
                }, 10);
                return true;
            });
        });
    };
})(jQuery);

function addNewInvite() {
    var confirm_key = $("#confirm_key").val();
    var comment = '';
    if ($("#comment")) {
        comment = $("#comment").val();
    }

    if (!confirm_key) {
        alert("No invite key set");
        return false;
    }

    $.ajax({
        data: {
            cmd: 'addCustomInvite',
            confirm_key: confirm_key,
            comment: comment
        },
        success: function(data) {
            if (data.status === 'success') {
                window.location.reload();
            } else {
                alert(data.message);
            }
        }
    });
}

function openSharePopup(url_to_share, provider, additional_text) {
    var width  = 575;
    var height = 400;
    var left   = ($(window).width()  - width)  / 2;
    var top    = ($(window).height() - height) / 2;
    var opts   = 'status=1' +
                 ',width='  + width  +
                 ',height=' + height +
                 ',top='    + top    +
                 ',left='   + left;

    var url = '';
    var popup_name = '';

    if (provider === 'twitter') {
        url = "https://twitter.com/share?text=" + encodeURIComponent(additional_text) +
                "&url=" + encodeURIComponent(url_to_share);
        popup_name = "Twitter";
    } else if (provider === 'linkedin') {
        url = "http://www.linkedin.com/shareArticle?mini=true&url=" + encodeURIComponent(url_to_share) +
                "&summary=" + encodeURIComponent(additional_text);
        popup_name = "LinkedIn";
    } else if (provider === 'google') {
        url = "https://plus.google.com/share?url=" + encodeURIComponent(url_to_share);
        popup_name = "Google Plus";
    } else if (provider === 'facebook') {
        url = "http://www.facebook.com/sharer.php?s=100&p[title]=&p[summary]="+encodeURIComponent(additional_text)+"&p[url]="+encodeURIComponent(url_to_share);
        popup_name = "Facebook";
    } else {
        return false;
    }

    window.open(url, popup_name, opts);
}

function sendShareLink() {
    $("#share_link_form").validationEngine({ scroll: false });
    if (!$("#share_link_form").validationEngine('validate')) {
        return false;
    }

    var data = $("#share_link_form").serialize();

    $.ajax({
        url: '/cmd.php?cmd=sendShareLink',
        data: data,
        success: function(data) {
            if (data.status === 'success') {
                $("#share_link_success").show();
                $("#share_link_inputs, #share_link_send").hide();
                $("#share_link_popup_close").text("Close");
                $(".popup_input_row input").val("");
            } else {
                alert(data.message);
            }
        }
    });

    return false;
}

function showMailSharePopup(additional_text, link) {
    $("#share_link_popup_close").text("Cancel");
    $("#share_link_success").hide();
    $("#share_link_inputs, #share_link_send").show();
    $("#share_link_text").text(additional_text + ' '+ link);
    $("#popup_share_link_container").show();
    return false;
}

function repost(link) {
    if (!is_logged) {
        window.location.href = login_url;
        return false;
    }

    if (link.hasClass('reposted')) {
        return flase;
    }

    var post_container = link.parents(".post_container");

    var type = post_container.attr('data-postType');
    var id = post_container.attr('data-postId');

    var data = {};
    data.cmd = 'repost';
    data.post_type = type;
    data.post_id = id;

    $.ajax({
        data: data,
        success: function(data) {
            if (data.status === 'success') {
                $(location).attr('href', data['result_url']);
            } else {
                alert(data.message);
            }
        }
    });
}


/* Get images from URL functions */

var first_image = true;
var images = [];
var current_image = 0;
var last_url = '';

function getUrlData(url) {
    url = $.trim(url);
    if (!url) {
        resetImages();
        return false;
    }

    if (url === last_url) {
        return false;
    }

    $("#title_input").attr('placeholder', 'Searching for a title...');

    last_url = url;

    $("#post_image_link").attr('href', url);
    $("#post_image_link").attr('target', '_blank');

    resetImages();
    $("#loader").show();
    $.ajax({
        data: {
            cmd: 'get_images_by_url',
            url: url
        },
        success: function(data) {
            if (data.status === 'success') {
                $("#title_input").removeAttr('placeholder').keyup();

                if (data.data.h1_title && !$("#title_input").val().trim()) {
                    $("#title_input").val(data.data.h1_title);
                }
                updateUrlImages(data.data.img_links);
            } else {
                alert(data.message);
                resetImages();
            }
        }
    });
}

function addValidImageToRotate(url) {
    images.push(url);
    $('#img_count').text(images.length);

    if (current_image === 0) {
        current_image = 1;
        $('#img_current, #img_count, .post_image_selector_arrow').show();
        $('#img_of_word').text(' of ');
        $('#img_current').text(current_image);
        $("#post_image").attr('src', images[current_image - 1]);
        $("#post_image").css('height', 'auto');
        if (first_image) {
            first_image = false;
        }
    }
}

function validateImageUrl(url) {
    var new_img = $("<img>");
    new_img.addClass('hide');
    new_img.load(function() {
        if ($(this).prop("width") <= 100) {
            $(this).remove();
            return;
        }

        addValidImageToRotate($(this).attr('src'));
        $(this).remove();
    }).attr('src', url);
}

function resetImages() {
    $('#img_current, #img_count, .post_image_selector_arrow').hide();
    $('#img_of_word').text('No images');
    $("#post_image").attr('src', '');
    $("#post_image").css('height', '90px');
    $("#loader").hide();
    images = [];
    current_image = 0;
}

function updateUrlImages(ajax_images) {
    if (ajax_images.length === 0) {
        resetImages();
        $("#loader").hide();
        return;
    }

    for (var i = 0; i < ajax_images.length; i++) {
        validateImageUrl(ajax_images[i]);
    }

    $("#loader").hide();
}

function rotateImg(forward) {
    var shift = forward ? 1 : -1;
    current_image += shift;
    if (current_image > images.length) {
        current_image = 1;
    } else if (current_image <= 0) {
        current_image = images.length - 1;
    }
    $('#img_current').text(current_image);
    $("#post_image").attr('src', images[current_image - 1]);
}

/* End of Get images from URL functions */

function updateUserFSponsorEmail() {
    var sponsor_email_chk = $('#get_sponsor_email_chk_thanks');

    if (sponsor_email_chk.length) {
        var data = {};
        data.cmd = 'updateUserFSponsor';
        data.f_get_sponsor_email = sponsor_email_chk.is(':checked') ? 1 : 0;

        $.ajax({
            data: data,
            success: function(data) {
                if (data.status === 'success') {
                    if (data.need_refresh) {
                        location.reload();
                    }
                } else {
                    alert(data.message);
                }
            }
        });
    }
}