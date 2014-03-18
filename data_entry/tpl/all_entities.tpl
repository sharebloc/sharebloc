{include file='header.tpl'}
<div id="container">
    <div id="sizer">
        {include file='menu.tpl'}
        <div class="page_block">
            <div class="pageTitle">All Entities</div>

            {if $err_msg}
                <div class="error_msg">{$err_msg}</div>
                <div class="cancel_btn_div">
                    <a class="btn verify_btn" href="/results.php">Back</a>
                </div>
            {else}

                <div id="ready_vendors_div">
                    <div class="">
                        Status filter:&nbsp;&nbsp;
                        <select id="status_filter" name="status_filter">
                            <option value="all">all</option>
                            <option value="new">new</option>
                            {if $dont_show_more === 'false'}
                                <option value="deleted">deleted</option>
                            {/if}
                            <option value="completed">completed</option>
                            <option value="ready">ready</option>
                            <option value="verified">verified</option>
                            {if $dont_show_more === 'false'}
                                <option value="exported">exported</option>
                            {/if}
                        </select>
                </div>
                     <div class="vendors_table_div">
                        <div class="simple_text_div">
                            <input class="dont_show_other center_checkbox" type="checkbox" name="dont_show_other" value="{$dont_show_more}" {if $dont_show_more==='true'}checked="checked"{/if}>&nbsp;&nbsp;Don't show Deleted and Exported entities
                        </div>
                        <br>
                        <table id="loaded_vendors" class="vendors_table small_font_table">
                            <tr class="th_row">
                                <th class="checkbox_column">
                                    <input class="check_all" type="checkbox" name="check_all" value="" >
                                </th>
                                <th class="name_column">Name</th>
                                <th class="field_name_column">Type</th>
                                <th class="field_name_column">Status</th>
                                <th class="field_name_column">Is duplicate</th>
                            </tr>
                            {foreach from=$entities item=v}
                                <tr class="{$v.status}" data-vendorId="{$v.id}">
                                    <td>
                                        <input class="check_vendor" data-vendorId="{$v.id}" type="checkbox" name="" value="">
                                    </td>
                                    <td>{$v.vendor}</td>
                                    <td>{$v.type|capitalize}</td>
                                    <td>{$v.status|capitalize}</td>
                                    <td>{if $v.is_duplicate}by {$v.is_duplicate}{/if}</td>
                                </tr>
                            {/foreach}
                        </table>
                    </div>
                    <form id="delete_vendors_form" action="/all_entities.php" method="POST">
                        <input type="hidden" id="ids_to_delete" name="ids_to_delete" value="">
                        <input type="hidden" id="dont_show_more" name="dont_show_more" value="true">
                        <input type="hidden" id="filter" name="filter" value="">
                    </form>
                    <div class="btn_div">
                        <a class="btn verify_btn" href="/results.php">Back</a>
                        <a class="btn delete" href="javascript:void(0)">Delete</a>
                    </div>
                </div>
            </div>
            {/if}
        </div>
    </div>
</div>

{include file='js_common.tpl'}
<script>

    var filter = '{$filter}';

    $(document).ready(function() {

        $('#status_filter').change(function () {
            $('.check_vendor').removeAttr("checked");
            $('.check_all').removeAttr("checked");
            var filter = $(this).val();
            if (filter === 'all') {
                $('#loaded_vendors tr').removeClass('hide');
                return false;
            }
            $('#loaded_vendors tr').addClass('hide');
            $('.th_row').removeClass('hide');
            $('.'+filter).removeClass('hide');
        });

        $("#status_filter [value='"+filter+"']").attr("selected", "selected");
        $('#status_filter').change();

        $('.check_all').click(function(){
            if ($(this).attr('checked')) {
                $('.check_vendor').each(function(){
                    if (!$(this).parent().parent().hasClass('hide')) {
                        $(this).attr("checked", "checked");;
                    }
                });
            } else {
                $('.check_vendor').removeAttr("checked");
            }
        });

        $('.dont_show_other').click(function(){
            if ($(this).attr('checked')) {
                $(this).val('true');
            } else {
                $(this).val('false');
            }
            $('#dont_show_more').val($(this).val());
            $('#filter').val($('#status_filter').val());
            $('#delete_vendors_form').submit();
        });

        $('.delete').click(function(){
            var checked_elements = $('input.check_vendor:checked');

            if (!checked_elements.length) {
                alert ("Please, select vendors or companies for delete");
                return false;
            }

            if (!confirm("Do you really want to delete selected vendors or companies from DB?")) {
                return false;
            }

            var ids_to_delete = getCheckedIds(checked_elements);

            $('#ids_to_delete').val(ids_to_delete);
            $('#dont_show_more').val($('.dont_show_other').val());
            $('#filter').val($('#status_filter').val());
            $('#delete_vendors_form').submit();
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

</script>

{include file='footer.tpl'}