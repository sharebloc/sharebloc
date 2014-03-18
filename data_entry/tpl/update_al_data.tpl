{include file='header.tpl'}
<div class="page_block">
    <div id="container">
        <div id="sizer">
            {include file='menu.tpl'}

            Done


    </div>
</div>
</div>
{include file='js_common.tpl'}

    <script>



    $(document).ready(function() {

        $("#login_button").click(function(){
            if (!validate()) {
                return false;
            }
            $("#login_form").submit();
        });
        $("#clear_data").click(function(){
            if (!confirm("Do you really want to clear all imported vendors?")) {
                return false;
            }
            var post_params = {
                type: "clearData",
                id: 1};
            $.ajax({ data: post_params });
            return false;
        });

        $('.login_field').keypress(function (e) {
            if (e.which == 13) {
                $('#login_button').click();
            }
        });
    });

    function onServerAnswer(data) {
        alert("Data cleared.");
        $("#clear_data").hide();
    }
    function validate() {
        var temp = true;
        $('input').each(function() {
            if(!$(this).val()) {
                temp  = false;
                $(this).css('border', '2px solid red');
            }
        });
        return temp;
    }


    </script>


{include file='footer.tpl'}