{include file='header.tpl'}
<div id="container">
    <div id="sizer">
        {include file='menu.tpl'}
        <div class="page_block">

            <div class="pageTitle">Names editing</div>

            {if $err_upload}
                <div class="error_msg">{$err_upload}</div>
            {/if}
            {if $err_msg}
                <div class="error_div">
                    <div class="error_msg">These lines have been ignored because of the incorrect format:</div>
                    {foreach from=$err_msg item=line}
                        {$line}<br>
                    {/foreach}
                    <br>
                    The correct format is:<br>
                    vendor_name, source<br>
                    where the first ',' is the delimiter<br>
                </div>

            {/if}

            <form id="clean_names" method="POST" action="/duplicates_name.php">
                <input id="cmd" name="cmd" type="hidden" value="cleaned">
                {if $vendors && $cleaned_count}
                    <div>
                        <a class="btn nextBtn" href="javascript:void(0)">Next</a>
                        <a class="btn startBtn" href="/import_file.php?start_over=true">Start over</a>
                    </div>
                    <div class="vendors_table_div clean_table_div">
                        <table id="loaded_vendors" class="vendors_table">
                            <tr>
                                <th>Source name</th>
                                <th>Edited name</th>
                            </tr>
                            {foreach from=$vendors item=v}
                                {if $v.name_cleaned}
                                    <tr class="vendor_row">
                                        <td>
                                            {$v.vendor_raw|escape}
                                        </td>
                                        <td>
                                            <input class="input-medium cleaned_name" type="text" id="vend_{$v.id}" name="vendor[{$v.id}]" value="{$v.vendor|escape}">
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
                    <a class="btn startBtn"  href="/import_file.php?start_over=true">Start over</a>
                </div>
            </form>
        </div>
    </div>
</div>

{include file='js_common.tpl'}
<script>


    $(document).ready(function() {
        $(".nextBtn").click(function(){
            $("#clean_names").submit();
        });


        $(".startBtn").click(function(){
            return confirm("Do you really want to delete imported data?");
        });
    });

</script>

{include file='footer.tpl'}