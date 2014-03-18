{include file='header.tpl'}
<div class="page_block">
    <div id="container">
        <div id="sizer">
            {include file='menu.tpl'}

            {if $err_msg}
                <div class="error_msg">{$err_msg}</div>
            {else}
                <div class="pageTitle">{$vendor.vendor}</div>
                <div class="f11em">Review {$network.display_name} profile link</div>
                <div class="review_div">
                    <table class="review_table">
                        <tr>
                            <td class="link_td"><a class="popup_link" href="{$vendor.$step_id.google_search_url}" target="blank">{$network.display_name}</a>
                                <br>
                                <span class="small_font">(<a class="popup_link" href="{$vendor.$step_id.network_link_source}" target="blank">the first result from Google</a>):</span>
                            </td>
                            <td class="url_td">
                                <a id="url" class="popup_link" href="{if $vendor.$step_id.network_link}{$vendor.$step_id.network_link}{else}none{/if}" target="blank">{if $vendor.$step_id.network_link}{$vendor.$step_id.network_link}{else}none{/if}</a>

                                <form class="hide" id="edit_url_form" method="POST" action="/search_profile.php">
                                    <br>
                                    new URL: <input class='url_input' type="text" id="new_url" name="new_url" value="{$vendor.$step_id.network_link}">
                                    <br><br>
                                    <a class="popup_link" href="{$vendor.$step_id.google_search_url}" target="blank">Search in Google</a>
                                    <br><br>
                                    <a class="btn noProfileBtn" href="javascript:void(0)">I can't find profile</a>
                                    <input type="hidden" name="current_step" value="{$step}">
                                    <input type="hidden" id="next_step" name="next_step" value="{$step+1}">
                                    <input type="hidden" id="should_save" name="should_save" value="1">
                                </form>
                            </td>
                            <td class="btn_td">
                                <a class="btn repairBtn" href="javascript:void(0)">Incorrect</a>
                                <a class="btn cancelBtn hide" href="javascript:void(0)">Cancel</a>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="back_next_div">
                    {if $step>0}
                        <a class="btn backBtn" href="javascript:void(0)">Back</a>
                    {/if}
                        <a class="btn nextBtn" href="javascript:void(0)">Next</a>
                </div>
                {if $show_long_operation_msg}
                <div class="back_next_div">
                    (next step will take about 10 seconds)
                </div>
                {/if}

                {include file='ready_links.tpl'}

            {/if}
        </div>
    </div>
</div>
{include file='js_common.tpl'}

<script>

    var current_step = {$step};
    var popupWin = null;
    var popup_params = '{$popup_params}';

    $(document).ready(function() {

        $(".repairBtn").click(function(){
            $("#edit_url_form").show();
            $(this).hide();
            $(".cancelBtn").show();
        });

        $(".cancelBtn").click(function(){
            $('#new_url').val($('#url').attr('href'));
            $("#edit_url_form").hide();
            $(this).hide();
            $(".repairBtn").show();
        });

        $(".backBtn").click(function(){
            $('#next_step').val(current_step-1);

            if (was_edited()) {
                var url_entered = $('#new_url').val().trim();
                if (url_entered) {
                    if (!confirm("Do you want to save current url?")) {
                        return false;
                    }
                }
                ajaxUrlCheck($('#new_url').val());
                return false;
            }

            if ($('#url').attr('href') === 'none') {
                nextPage();
            } else {
                ajaxUrlCheck($('#url').attr('href'));
            }
            return false;
        });

        $(".nextBtn").click(function(){
            $('#next_step').val(current_step+1);

            if (was_edited()) {
                var url_entered = $('#new_url').val();
                if (!url_entered) {
                    alert ("Please enter a value or click the 'I can't find profile' button.");
                    return false;
                }
                ajaxUrlCheck($('#new_url').val());
                return false;
            }

            if ($('#url').attr('href') === 'none') {
                nextPage();
            } else {
                ajaxUrlCheck($('#url').attr('href'));
            }
            return false;
        });

        $(".noProfileBtn").click(function() {
            $('#new_url').val('');
            nextPage();
        });

        $('.popup_link').click(function(){
            if ($(this).attr('href')==='none') {
                return false;
            }
            if(popupWin) {
                if(popupWin.closed) {
                    popupWin = window.open($(this).attr('href'), 'popup', popup_params+',location=1,resizable=1');
                } else {
                    popupWin.location.replace($(this).attr('href'));
                }
            } else {
                popupWin = window.open($(this).attr('href'), 'popup', popup_params+',location=1,resizable=1');
            }
             popupWin.focus();
             return false;
        });
    });

    function was_edited() {
        var is_edit_active = $('#edit_url_form').is(':visible');
        if (!is_edit_active) {
            $('#should_save').val(0);
            return false;
        }

        $new_value = $('#new_url').val();
        if ($new_value==='') {
            // to compare with link above
            $new_value = 'none';
        }

        if ($new_value !== $('#url').attr('href')) {
            $('#should_save').val(1);
            return true;
        } else {
            $('#should_save').val(0);
            return false;
        }
    }

    function nextPage() {
        $("#edit_url_form").submit();
        return false;
    }

    function onServerAnswer(data) {
        if (data['message']) {
            if (confirm(data['message'])) {
                nextPage();
                return false;
            } else {
                return false;
            }
        } else {
           nextPage();
        }
    }
    function ajaxUrlCheck(url_to_check) {
        var post_params = {
                type: "checkUrl",
                url: url_to_check,
                step: current_step
        };
        $.ajax({ data: post_params });
        return false;
    }

</script>


{include file='footer.tpl'}