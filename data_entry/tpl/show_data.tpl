{include file='header.tpl'}
<div class="page_block">
    <div id="container">
        <div id="sizer">
            {include file='menu.tpl'}

            {if $err_msg}
                <div class="error_msg">{$err_msg}</div>
            {else}

                <div class="pageTitle">Full Vendor Info</div>

                <div class="simple_text_div">
                    <div class="show_line"><span class="title_text">Vendor Name:</span> {if $vendor.crawled_google_url.network_link}<a href="{$vendor.crawled_google_url.network_link}">{/if}{$vendor.vendor}{if $vendor.crawled_google_url.network_link}</a>{/if}</div>
                    <br>
                    {foreach from=$networks key=network_id  item=network_data}
                        {assign var="network_link" value="{$vendor.$network_id.network_link}"}
                        <div class="show_line"><span class="title_text">{$network_data.display_name} URL:</span>
                            {if $network_link}
                                <a class="popup_link" href="{$network_link}">{$network_link}</a>
                            {else}
                                No profile
                            {/if}
                        </div>
                    {/foreach}
                    <br>
                    <div class="show_line"><span class="title_text">City:</span>{if $vendor.city_source != 'none'} {$vendor[$vendor.city_source].data.city|escape} {/if}</div>
                    <div class="show_line"><span class="title_text">State or Country:</span>{if $vendor.country_source != 'none'} {$vendor[$vendor.country_source].data.country|escape} {/if}</div>
                    {if $vendor.type=='company'}
                        <div class="show_line"><span class="title_text">Size:</span>{if $vendor.size_source != 'none'} {$vendor[$vendor.size_source].data.size|escape} {/if}</div>
                        <div class="show_line"><span class="title_text">Industry:</span>{if $vendor.industry_source != 'none'} {$vendor[$vendor.industry_source].data.industry|escape} {/if}</div>
                    {/if}
                    <div class="show_line"><span class="title_text">Logo:</span>{if $vendor.logo_source != 'none'} {if {$vendor[$vendor.logo_source].data.logo}} <img class="logo_img logo_img_show" src="{$vendor[$vendor.logo_source].data.logo}">{/if} {/if}</div>
                    <div class="show_line"><span class="title_text">Description:</span>{if $vendor.description_source != 'none'} {$vendor[$vendor.description_source].data.description|escape|nl2br}{/if}</div>
                </div>
                <div class="back_next_div">
                    {if $can_save}
                        <a class="btn firstStepBtn" href="javascript:void(0)">Start over</a>
                        <a class="btn backBtn" href="javascript:void(0)">Back</a>
                        <a class="btn" href="/show_data.php?save_data=1">Confirm</a>
                    {else}
                        <span class="title_text">Data Saved</span>
                        <br>
                        <br>
                        <a class="btn" href="/index.php?clear_vendor=true">Home</a>
                    {/if}
                </div>
                <form id="first_step_form" name="first_step_form" action="/search_profile.php" method="POST">
                    <input type="hidden" id="next_step" name="next_step" value="0">
                    <input type="hidden" name="current_step" value="-1">
                </form>
                <form id="back_form" name="back_form" action="/select_data.php" method="POST">
                    <input type="hidden" id="next_step" name="next_step" value="{$back_step}">
                </form>
            {/if}
        </div>
    </div>
</div>
{include file='js_common.tpl'}

<script>
    var popup_params = '{$popup_params}';

    $(document).ready(function() {
        $(".logo_img").fullImgClick();

        $(".backBtn").click(function(){
            $("#back_form").submit();
        });

        $(".firstStepBtn").click(function(){
            $("#first_step_form").submit();
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
</script>


{include file='footer.tpl'}