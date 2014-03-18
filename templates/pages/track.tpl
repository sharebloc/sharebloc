{include file='components/header_new.tpl'}

<div class="tracks_container">

    <div class="tracks_left_div" >
        <div class="tracks_types_div">
            {if $show_old_types}
                <a href="#" id="show_all_types" class="tracks_link">Show old types</a>
                <br><br><br><br>
            {/if}
            {foreach from=$types item=type key=key}
                <div class="track_type_div {if $type.old}hide{/if}">
                    <a href="#" id="tracks_link_{$key}" data-type="{$key}" class="tracks_link">{$type.display_name}</a>
                    <a href="{$base_url}/cmd.php?cmd=get_tracks&type={$key}&csv=1" target="_blank" class="tracks_link_csv">csv</a>
                    <br><br>
                </div>
            {/foreach}
        </div>
        <div class="tracks_descr_div"></div>
        <div class="tracks_query_div"></div>
    </div>

    <div class="tracks_right_div">
        <div class="tracks_table_div">
        </div>
        <div class="front_loader_div hide"><img src="/images/loading.gif"></div>
    </div>
    <div class="clear"></div>
</div>
{include file='components/js_common.tpl'}
<script>
    var loading = false;
    $.ajaxSetup({
      timeout: 360000
    });
    $("document").ready(function() {

        $(document).on("click", ".tracks_link", function(event) {
            if ($(this).attr('id')==='show_all_types') {
                $(".track_type_div").show();
                return false;
            }

            getTracks($(this));
            return false;
        });

        $(document).on("click", ".disable_user_btn", function(event) {
            if (!confirm("Confirm you want to disable this user. Some data will be lost. Are you sure?")) {
                return false;
            }
            disableUser($(this));
            return false;
        });
    });

    function getTracks(link) {
        if (loading) {
            return false;
        }

        loading = true;

        var type = link.attr('data-type');
        var subtype = link.attr('data-subtype');

        var params = {};

        $(".track_params_div .track_param").each(function() {
            var name = $(this).attr('name');
            var value = $(this).val();
            params[name] = value;
        });

        $(".tracksTable").remove();
        $(".track_params_div").remove();
        $(".front_loader_div").show();

        $.ajax({
            data: {
                cmd: 'get_tracks',
                type: type,
                subtype: subtype,
                params: params
            },
            success: function(data) {
                loading = false;
                $(".front_loader_div").hide();
                if (data.status === 'success') {
                    updateTracks(data.data);
                } else {
                    alert(data.message);
                }
            }
        });
    }

    function updateTracks(data) {
        var table = $(data['html']);

        $(".tracks_table_div").append(table);
        $(".tracks_descr_div").html(data.descr);
        $(".tracks_query_div").html(data.query);
        $(".tracks_link").removeClass("selected_type");
        $("#tracks_link_" + data.type).addClass("selected_type");
    }

    function disableUser(button) {
        var user_id = button.attr('data-userId');
        var row_id = button.parents('tr').attr('data-rowID');
        $.ajax({
            data: {
                cmd: 'disableUser',
                user_id: user_id
            },
            success: function(data) {
                if (data.status === 'success') {
                    $("#data_row_" + row_id).addClass('user_deleted');
                } else {
                    alert(data.message);
                }
            }
        });
    }
</script>

{include file='components/footer_new.tpl'}