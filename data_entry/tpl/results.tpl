{include file='header.tpl'}
<div id="container">
    <div id="sizer">
        {include file='menu.tpl'}
        <div class="page_block">

            <ul class="nav nav-tabs">
                <li><a href="/import_file.php">Import</a></li>
                <li><a href="/users.php">Accounts</a></li>
                <li class="active">
                    <a href="/results.php">Results & Export</a>
                </li>
            </ul>
            <div class="simple_text_div">
                <table class="vend_stat_table">
                    <tr>
                        <td class="stat_name">
                            <a href="/all_entities.php">All entities:</a>
                        </td>
                        <td class="vend_number tright">{$all_entities_count}</td>
                    </tr>
                    <tr>
                        <td class="stat_name">Deleted:</td>
                        <td class="vend_number tright">{$deleted_count}</td>
                    </tr>
                    <tr>
                        <td class="stat_name">In a queue:</td>
                        <td class="vend_number tright">{$in_queue_count}</td>
                    </tr>
                    <tr>
                        <td>Exported:</td>
                        <td class="tright">{$exported_count}</td>
                    </tr>
                    <tr>
                        <td>
                            {if $ready_for_export_count>0}
                                <a href="/ready_vendors.php">Ready for export:</a>
                            {else}
                                Ready for export:
                            {/if}
                        </td>
                        <td class="tright">{$ready_for_export_count}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

{include file='js_common.tpl'}
<script>
</script>

{include file='footer.tpl'}