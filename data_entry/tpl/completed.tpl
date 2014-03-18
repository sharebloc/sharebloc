{include file='header.tpl'}
<div id="container">
    <div id="sizer">
        {include file='menu.tpl'}
        <div class="page_block">

            <div class="pageTitle">Vendors queued</div>


            {if $vendors_count}
                <div class="vendors_table_div">
                    <table id="loaded_vendors" class="vendors_table">
                        <tr>
                            <th>Vendor Name</th>
                        </tr>
                    {foreach from=$vendors item=v}
                        <tr class="vendor_row">
                            <td>
                            {$v.vendor|escape}{if $v.crawled_google_url}, {$v.crawled_google_url}{/if}
                            </td>
                        </tr>
                    {/foreach}
                    </table>
                </div>
            {else}
                <div class="no_records">
                    No vendors to queue.
                </div>
            {/if}
            <div>
                    <a class="btn" href="/index.php">Done</a>
            </div>
        </div>
    </div>
</div>

{include file='js_common.tpl'}
<script>


$(document).ready(function() {
});

</script>

{include file='footer.tpl'}