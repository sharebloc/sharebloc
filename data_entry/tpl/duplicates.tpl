{include file='header.tpl'}

<div id="container">
    <div id="sizer">
        <div class="page_block">
            {include file='menu.tpl'}
            <div class="page_content">
                <div class="pageTitle">Duplicates by {if $url_page}url{else}name{/if}</div>
                {if $google_banned}
                    <div class="error_div">
                        <div class="error_msg">URLs for this vendors have not been received by Google search.<br>
                            May be our robot is banned.</div>
                            {foreach from=$google_banned item=line}
                                {$line}<br>
                        {/foreach}
                    </div>

                {/if}

                <form id="del_dupl" action="{if $url_page}/completed.php{else}/duplicates_url.php{/if}" method="POST">
                    <input type="hidden" id="vend_to_delete" name="vend_to_delete" value="">
                    <input id="cmd" name="cmd" type="hidden" value="del_dupl">
                </form>

                {if $duplicates_count}
                    <div>
                        <a class="btn nextBtn" href="javascript:void(0)">Next</a>
                        <a class="btn startBtn" href="/import_file.php?start_over=true">Start over</a>
                    </div>
                    <br>
                    DE stands for Data Entry tables, VS for Vendostack tables
                    <div class="vendors_table_div">
                        <input type="hidden" id="deleted_vend" value="">
                        <table id="loaded_vendors" class="vendors_table">
                            <tr>
                                <th class="button_header"></th>
                                <th class="new_vendor_header">New Vendor</th>
                                <th class="duplicates_header">Duplicates</th>
                            </tr>
                            {foreach from=$vendors item=v}
                                {if $v.duplicates.vs || $v.duplicates.de}

                                    <tr id="vend_id_{$v.id}" data-vendorId="{$v.id}"  class="vendor_row red_row deleted">
                                        <td>
                                            <a id="keep_{$v.id}" data-vendorId="{$v.id}" class="btn keepBtn hide" href="javascript:void(0)">Keep It</a>
                                            <a id="del_{$v.id}" data-vendorId="{$v.id}" class="btn delBtn hide" href="javascript:void(0)">Remove</a>
                                        </td>
                                        <td>
                                            {$v.vendor|escape}
                                            {if $url_page}
                                                <br>
                                                {if $v.crawled_google_url}
                                                    <span class="vendor_url">{$v.crawled_google_url}</span>
                                                {else}
                                                    URL not found
                                                {/if}
                                            {/if}
                                        </td>

                                        <td>
                                            {if $v.duplicates.de || $v.duplicates.vs}
                                                {foreach from=$v.duplicates.vs item=vs_name_dupl}
                                                    VS: {if $v.type=='vendor'}{$vs_name_dupl.vendor_name}{else}{$vs_name_dupl.company_name}{/if} (by {if $url_page}url{else}name{/if})<br>
                                                    {if $url_page}
                                                        <span class="vendor_url">{$vs_name_dupl.website}</span><br>
                                                    {/if}
                                                {/foreach}
                                                {foreach from=$v.duplicates.de item=de_name_dupl}
                                                    DE: {$de_name_dupl.vendor} (by {if $url_page}url{else}name{/if})<br>
                                                    {if $url_page}
                                                        <span class="vendor_url">{$de_name_dupl.crawled_google_url}</span><br>
                                                    {/if}
                                                {/foreach}
                                            {else}
                                                No duplicates found
                                            {/if}
                                        </td>
                                    </tr>
                                {/if}
                            {/foreach}
                        </table>
                    </div>
                {else}
                    <div class="no_records">
                        This validation step found no records to fix.
                    </div>
                {/if}

                <div>
                    <a class="btn nextBtn" href="javascript:void(0)">Next</a>
                    <a class="btn startBtn" href="/import_file.php?start_over=true">Start over</a>
                </div>
            </div>
            <div class="hide" id="ajax_progress">
                <div class="pageTitle">Crawling in progress</div>
                <div>Please do not close the browser</div>
                <div class="progress_div">
                    <span id="ready_vendors">0</span>/<span id="all_vendors">0</span> vendors are ready
                </div>
            </div>
        </div>
    </div>
</div>


{include file='js_common.tpl'}
<script>


    $(document).ready(function() {

        $(".keepBtn").show();

        var time_per_vendor = {$time_per_vendor};
        var totalVendorsCount = {$total_count};
        var refresh_interval = null;

        $(".nextBtn").click(function(){
            var v_count = $(".red_row").length;
            var next_step_count = totalVendorsCount - v_count;
            var time =  next_step_count * time_per_vendor;

            if (time > 10) {
                var time_string = time + " seconds";
                if (time > 60) {
                    var time_string =  div(time, 60)+ " minutes";
                }
                alert("Next step will take some time ("+time_per_vendor+"s per vendor - ~"+time_string+ " for this import ) to be not banned by Google");
            }

            $('.deleted').each(function() {
                var vend_id = $(this).attr('data-vendorId');
                if ($("#vend_to_delete").val()) {
                    $("#vend_to_delete").val($("#vend_to_delete").val()+","+vend_id);
                }
                else {
                    $("#vend_to_delete").val(vend_id);
                }
            });

            var url_page = '{$url_page}';
            if (url_page) {
                $("#del_dupl").submit();
            } else {
                $(".page_content").hide();
                ajaxDelDuplAndCrawlProfiles();
                $("#all_vendors").text(next_step_count);
                $("#ajax_progress").show();
                refreshPageData();
            }
            return false;
        });



        $(".keepBtn").click(function(){
            var vend_id = $(this).attr('data-vendorId');

            $("#vend_id_"+vend_id).removeClass('red_row');
            $("#vend_id_"+vend_id).addClass('green_row');
            $("#vend_id_"+vend_id).removeClass('deleted');

            $(this).toggle();

            $("#del_"+vend_id).toggle();
        });

        $(".delBtn").click(function(){
            var vend_id = $(this).attr('data-vendorId');

            $("#vend_id_"+vend_id).removeClass('green_row');
            $("#vend_id_"+vend_id).addClass('red_row');
            $("#vend_id_"+vend_id).addClass('deleted');

            $("#keep_"+vend_id).toggle();

            $(this).toggle();
        });

        $(".startBtn").click(function(){
            return confirm("Do you really want to delete imported data?");
        });

    });

    function onServerAnswer(data) {
        if (data.type==='getReadyVend') {
            $("#ready_vendors").text(data.ready);
            $("#all_vendors").text(data.all);
            if (data.ready === data.all) {
                clearInterval(refresh_interval);
                $("#del_dupl").submit();
                return false;
            }
        }
    }

    function ajaxDelDuplAndCrawlProfiles() {
        var post_params = {
                type: "delDupl",
                vend_to_delete: $("#vend_to_delete").val()
        };
        $.ajax({
                timeout: 10000,
                success: function(){ },
                error: function(){ },
                data: post_params
                });
        return false;
    }

    function refreshPageData() {
        var refresh_timeout = {$ajax_refresh};
        refresh_interval = setInterval(ajaxGetNumberOfReadyVendors, 1000*refresh_timeout);
    }

    function ajaxGetNumberOfReadyVendors() {
        var post_params = {
                type: "getReadyVend"
        };
        $.ajax({ data: post_params });
        return false;
    }

   function div(val, by){
        return (val - val % by) / by;
    }

</script>

{include file='footer.tpl'}