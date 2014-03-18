{include file='header.tpl'}
<div id="container">
    <div id="sizer">
        {include file='menu.tpl'}
        <div class="page_block">
            <div class="pageTitle">Entities ready for export</div>

            {if $err_msg}
                <div class="error_msg">{$err_msg}</div>
                <div class="cancel_btn_div">
                    <a class="btn verify_btn" href="/results.php">Back</a>
                </div>
            {else}

                <div id="ready_vendors_div">
                     <div class="vendors_table_div">
                        <table id="loaded_vendors" class="vendors_table">
                            <th>
                                <input class="check_all" type="checkbox" name="check_all" value="" >
                            </th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Worker Email</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                                {foreach from=$vendors item=v}
                                <tr>
                                    <td>
                                        <input class="check_vendor" data-vendorId="{$v.id}" type="checkbox" name="" value="">

                                    </td>
                                    <td>
                                        <a href="/verify_vendor.php?id={$v.id}">{$v.vendor}</a>
                                    </td>
                                    <td>{$v.type|capitalize}</td>
                                    <td>{$v.worker}</td>
                                    <td>
                                        {if $v.status=='verified'}
                                            Verified
                                        {else}
                                            &nbsp;
                                        {/if}
                                    </td>
                                    <td>
                                        {if $v.all_errors}
                                            <img class="excl_picture" src="/img/exclamation.png" title="{$v.all_errors}">
                                        {/if}
                                    </td>
                                </tr>
                            {/foreach}
                        </table>
                    </div>
                    <form id="delete_vendors_form" action="/ready_vendors.php" method="POST">
                        <input type="hidden" id="ids_to_delete" name="ids_to_delete" value="">
                    </form>
                    <div class="btn_div">
                        <a class="btn verify_btn" href="/results.php">Back</a>
                        <a class="btn export" href="javascript:void(0)">Export</a>
                        <a class="btn delete" href="javascript:void(0)">Delete</a>
                    </div>
                </div>
                <div class="progress_div hide" id="exporting_div">
                    Please wait, the export is in progress
                </div>
            </div>
            {/if}
        </div>
    </div>
</div>

{include file='js_common.tpl'}
<script>
    var export_url = "{$vs_host}/cmd.php?cmd=import_vendors_from_de";

    $(document).ready(function() {

        $('.check_all').click(function(){
            if ($(this).attr('checked')) {
                var checkboxes = $('.check_vendor');
                checkboxes.each(function() {
                    $(this).attr("checked", "checked");
                });
            } else {
                $('.check_vendor').removeAttr("checked");
            }
        });

        $('.delete').click(function(){
            var checked_elements = $('input.check_vendor:checked');

            if (!checked_elements.length) {
                alert ("Please, select vendors or companies for delete");
                return false;
            }

            if (!confirm("Do you really want to delete selected vendors or companies?")) {
                return false;
            }

            var ids_to_delete = getCheckedIds(checked_elements);
            $('#ids_to_delete').val(ids_to_delete);
            $('#delete_vendors_form').submit();
        });

        $('.export').click(function(){
            var checked_elements = $('input.check_vendor:checked');

            if (!checked_elements.length) {
                alert ("Please, select vendors or companies for export");
                return false;
            }

            var ids_to_export = getCheckedIds(checked_elements);
            ajaxExport(ids_to_export);

            $('#ready_vendors_div').hide();
            $('#exporting_div').show();
        });
    });

    function getCheckedIds(checked_elements) {
        var vendors_ids = [];

        checked_elements.each(function() {
            var vend_id = $(this).attr('data-vendorId');
            vendors_ids.push(vend_id);
        });
        var str_ids = vendors_ids.join(',');

        return str_ids;
    }

    function ajaxExport(vendors_ids) {
         $.ajax({
                url: export_url,
                success: function(data){ onExportDone(data);},
                error: function(){
                                alert('Error while exporting');
                                $('#ready_vendors_div').show();
                                $('#exporting_div').hide();
                                },
                data: { de_vendors_ids: vendors_ids}
        });
        return false;
    }

    function onExportDone(data) {
        alert(data.message);
        if (data.status=== 'success') {
           location.reload();
           return false;
        }
        $('#ready_vendors_div').show();
        $('#exporting_div').hide();
    }

</script>

{include file='footer.tpl'}