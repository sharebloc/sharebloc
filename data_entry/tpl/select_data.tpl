{include file='header.tpl'}
<div class="page_block">
    <div id="container">
        <div id="sizer">
            {include file='menu.tpl'}
            {if $err_msg}
                <div class="error_msg">{$err_msg}</div>
            {else}
                <div class="pageTitle">{$vendor.vendor}</div>
                {if $err_parse_msg}
                    <div class="error_msg">
                        {foreach from=$err_parse_msg item=line}
                            {$line}<br>
                        {/foreach}
                    </div>
                {/if}
                <div class="f11em">Select {$step_display_name}</div>
                <div class="select_data_div">
                    <form id="save_data_form" name="save_data_form" action="/select_data.php" method="POST">
                        <input type="hidden" name="current_step" value="{$step}">
                        <input type="hidden" id="next_step" name="next_step" value="{$step+1}">
                        <input type="hidden" id="should_save" name="should_save" value="1">
                        <table class="select_data_tbl">
                            <th class="radio_column">&nbsp;</th>
                            <th class="source_column">Sourse name</th>
                            {if $step==2}
                                <th class="size_column">Image size</th>
                            {/if}
                            <th class="value_column">Vendor {$step_display_name}</th>
                            {foreach from=$networks key=network_id  item=network_data}
                                {assign var="network_link" value="{$vendor.$network_id.network_link}"}
                                <tr>
                                    <td>
                                        {if $network_link}
                                            {if $vendor.$network_id.data.$step_id}
                                                <input class="radio" type="radio" name="source_id" value='{$network_id}' {if $selected_source_id==$network_id}checked{/if}>
                                            {else}
                                                &nbsp;
                                            {/if}
                                        {else}
                                            &nbsp;
                                        {/if}
                                    </td>
                                    <td>
                                        {if $network_link}
                                        <a class="popup_link" href="{$network_link}" target="blank">{$network_data.display_name}</a>:
                                        {else}
                                            {$network_data.display_name}
                                        {/if}
                                    </td>
                                    {if $step==2}
                                        <td>
                                            <span></span>
                                        </td>
                                    {/if}
                                    <td class="value_td">
                                        {if $network_link}
                                            {if $vendor.$network_id.data.$step_id}
                                                {if $step==2}
                                                    <img class="logo_img" src="{$vendor.$network_id.data.$step_id}">&nbsp; (Click on logo to view full-sized)
                                                {else}
                                                    {$vendor.$network_id.data.$step_id|escape|nl2br}
                                                {/if}
                                            {else}
                                                Not found
                                            {/if}
                                        {else}
                                            No profile
                                        {/if}
                                    </td>
                                </tr>
                            {/foreach}
                            <tr>
                                <td>
                                    <input class="radio" type="radio" name="source_id" value='none' {if {$selected_source_id}=='none'}checked {/if}>
                                </td>
                                <td>None of these</td>
                                <td colspan="2"></td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div class="back_next_div">
                    {if $step>0}
                        <a class="btn backBtn" href="javascript:void(0)">Back</a>
                    {else}
                        <a class="btn backToLastProfileBtn" href="javascript:void(0)">Back</a>
                    {/if}
                    <a class="btn nextBtn" href="javascript:void(0)">Next</a>
                    <form id="last_profile_step_form" name="last_profile_step_form" action="/search_profile.php" method="POST">
                        <input type="hidden" id="next_step" name="next_step" value="5">
                        <input type="hidden" name="current_step" value="4">
                    </form>
                </div>
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

        $('.logo_img').each(function() {
            var my_thumb = $(this);
            var big_img = $("<img>");
            big_img.load(function(){
                var big_img_height = big_img.prop("height");
                var big_img_width = big_img.prop("width");
                $(this).remove();
                my_thumb.parent('td').prev('td').children('span').append("Height: "+big_img_height+
                                            "px;<br>Width: "+big_img_width+"px");
            }).attr('src', my_thumb.attr('src'));
        });


        $(".nextBtn").click(function(){
            $('#should_save').val(1);
            $('#next_step').val(current_step+1);

            if ($('.radio:checked').val()) {
                $("#save_data_form").submit();
            } else {
                alert ('Please, select the {$step_display_name} source');
                return false;
            }
        });

        $(".backBtn").click(function(){
            $('#should_save').val(0);
            $('#next_step').val(current_step-1);
            $("#save_data_form").submit();
        });

        $(".backToLastProfileBtn").click(function(){
            $("#last_profile_step_form").submit();
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

        $(".logo_img").fullImgClick();


     });
</script>


{include file='footer.tpl'}