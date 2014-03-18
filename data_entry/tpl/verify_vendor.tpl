{include file='header.tpl'}
<div id="container">
    <div id="sizer">
        {include file='menu.tpl'}
        <div class="page_block">
            {if $err_msg}
                <div class="error_msg">{$err_msg}</div>
            {else}
                <div class="pageTitle">Vendor Info Verification</div>

                <div class="verify_table_div">
                    {if $vendor.logo_filename}
                        <div class="img_above_text_div">
                            <img class="verify_logo_img" src="/logos/{$vendor.logo_filename}" >
                        </div>
                    {/if}
                    <form id="verify_form" action="/verify_vendor.php" method="POST">
                        <input type="hidden" id="id" name="id" value="{$vendor.id}">
                        <input type="hidden" id="verify_data" name="verify_data" value="true">
                        <table class="verify_table">
                            <tr>
                                <td class="verify_name_column">Vendor Name:</td>
                                <td class="verify_val_column"><input class="verify_input" type="text" id="vendor" name="vendor" value="{$vendor.vendor}"></td>
                            </tr>
                            <tr>
                                <td>Source:</td>
                                <td><input class="verify_input" type="text" id="source" name="source" value="{$vendor.source}"></td>
                            </tr>
                            <tr>
                                <td>URL:</td>
                                <td><input class="verify_input" type="text" id="crawled_google_url" name="crawled_google_url" value="{$vendor.crawled_google_url}"></td>
                            </tr>
                            {foreach from=$networks key=network_id  item=network_data}
                                <tr>
                                    <td>{$network_data.display_name} URL:</td>
                                    <td>
                                        <input class="verify_input" type="text" id="{$network_id}" name="{$network_id}" value="{$vendor.$network_id}">
                                        <br> {if $vendor.link_errors.$network_id}<span class="link_error">{$vendor.link_errors.$network_id}</span>{/if}
                                    </td>
                                </tr>
                            {/foreach}
                            <tr>
                                <td>City:</td>
                                <td><input class="verify_input" type="text" id="city" name="city" value="{$vendor.city|escape}"></td>
                            </tr>
                            <tr>
                                <td>State or Country:</td>
                                <td><input class="verify_input" type="text" id="country" name="country" value="{$vendor.country|escape}"></td>
                            </tr>
                            {if $vendor.type=='company'}
                                <tr>
                                    <td>Size:</td>
                                    <td><input class="verify_input" type="text" id="size" name="size" value="{$vendor.size|escape}"></td>
                                </tr>
                                <tr>
                                    <td>Industry:</td>
                                    <td><input class="verify_input" type="text" id="industry" name="industry" value="{$vendor.industry|escape}"></td>
                                </tr>
                            {/if}
                            <tr>
                                <td>Logo:</td>
                                <td><input class="verify_input" type="text" id="logo" name="logo" value="{$vendor.logo}"></td>
                            </tr>
                            <tr>
                                <td>Description:</td>
                                <td><textarea class="verify_textarea" id="description" name="description">{$vendor.descr_html|escape}</textarea></td>
                            </tr>
                        </table>
                    </form>
                </div>
                <form id="delete_vendors_form" action="/ready_vendors.php" method="POST">
                    <input type="hidden" id="ids_to_delete" name="ids_to_delete" value="{$vendor.id}">
                </form>
                <div class="">
                    <a class="btn verify_btn" href="/ready_vendors.php">Cancel</a>
                    <a class="btn verify_btn" href="javascript:void(0)">Save and Verify</a>
                    <a class="btn del_btn" href="javascript:void(0)">Delete</a>
                </div>
            {/if}
        </div>
    </div>
</div>

{include file='js_common.tpl'}
<script>
    $(document).ready(function() {

        $('.verify_btn').click(function(){
            $('#verify_form').submit();
        });

        $('.del_btn').click(function(){
            var entity_type = '{$vendor.type}';

            if (!confirm("Do you really want to delete this "+entity_type+"?")) {
                return false;
            }

            $('#delete_vendors_form').submit();
        });

    });
</script>

{include file='footer.tpl'}