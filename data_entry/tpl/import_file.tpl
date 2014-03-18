{include file='header.tpl'}
<div id="container">
    <div id="sizer">
        {include file='menu.tpl'}
        <div class="page_block">

            <ul class="nav nav-tabs">
                <li class="active">
                  <a href="/import_file.php">Import</a>
                </li>
                <li><a href="/users.php">Accounts</a></li>
                <li><a href="/results.php">Results & Export</a></li>
            </ul>

            <div class="pageTitle">CSV file uploading</div>

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


            {if $empty_file_msg}
                <div class="error_div">
                    <div class="error_msg">{$empty_file_msg}</div>
                </div>
            {/if}

            <div class="content_block" id="tab_import">
                <form enctype="multipart/form-data" action="/import_file.php" method="POST" id="upload_form">
                    <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
                    <input type="hidden" name="cmd" id="cmd" value="upload_file">
                    <div class="select_file_div">
                        <span >Select file:&nbsp;&nbsp;</span>
                        <input id="userfile" name="userfile" class="" type="file" />
                        <br><br>
                        <div>
                            <div class="simple_text_div radio_block">
                                <input class="vend_type_radio" type="radio" name="list_type" value="vendor">&nbsp;&nbsp;List of the vendors
                            </div>
                            &nbsp;&nbsp;
                            <div class="simple_text_div radio_block">
                                <input class="vend_type_radio" type="radio" name="list_type" value="company">&nbsp;&nbsp;List of the companies
                            </div>
                        </div>
                        <a id="upload_button" class="btn upload_button">Upload file</a>
                    </div>
                </form>
            </div>
            <br>
            <br>
            <div class="simple_text_div">
                You can download this csv file to upload it:<br>
                <a href="/test_files/entry_data.csv">Test file </a> <br>
            </div>

        </div>
    </div>
</div>

{include file='js_common.tpl'}
<script>
    $(document).ready(function() {

    $("#upload_button").click(function(){
        if ($("#userfile").val()) {
            if (!$('.vend_type_radio:checked').length) {
                alert("You have to select the type of imported file");
                return false;
            }
            $("#upload_form").submit();
        }
        else {
            alert ("You have to select the file before uploading it");
        }
});

});
</script>

{include file='footer.tpl'}