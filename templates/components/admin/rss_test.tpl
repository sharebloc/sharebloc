{include file='components/header_new.tpl'}
<div class="page_sizer_wide rails_container">
    <form method="POST">
        <input type='hidden' name='crawl_test' value='1'/>
        <input type='hidden' name='do_test' value='1'/>

        To test RSS autopost script click <a href="/autopost_from_rss.php">here</a>
        <br><br><br>
        Enter RSS feed to parse:  <input name='rss_to_test' style='width:300px;' value='{$rss_to_test}'/>
        <br><br>
        <input type='radio' class="test_type_radio" name='test_type' checked value="only_show"/> Only show parsed feed
        <br>
        <input type='radio' class="test_type_radio" name='test_type' value="post_on_behalf" {if $is_live}disabled{/if}/> Post feed on behalf of: {if $is_live}<b>disabled on live</b>{/if}
        <br><br>
        <div class="behalf_params">
            <input type='radio' name='entity_type' checked value="user"/> User
            <br>
            <input type='radio' name='entity_type' value="vendor" /> Vendor
            <br><br>
            User/vendor id (codename is not supported now, David has id=2): <input name='entity_id' style='width:30px;' value='2'/>
            <br><br>
            Posts limit: <input name='crawl_limit' style='width:30px;' value='3'/>
            <br><br>
            Start from post: <input name='crawl_offset' style='width:30px;' value='0'/>
            <br><br>
        </div>
        <input type='submit' title='Submit!'>
    </form>
    <br><br>
    <hr>
    <br><br>
    {foreach $posts item=post}
        <hr style='margin-top:20px;'>
        <br><br>
        <h1>{$post.title}</h1>
        <br><br>
        <h3>{$post.raw_url}</h3>
        <br><br>
        <h4>{$post.pubDate}</h4>
        <br><br>
        <div>{$post.text}</div>
        <br><br>
    {/foreach}

</div>
{include file='components/js_common.tpl'}
<script>
    $(document).ready(function() {
        $(".test_type_radio").change(function(){
            if ($(this).val()==='only_show') {
                $(".behalf_params input").prop('disabled', true);
            } else {
                $(".behalf_params input").prop('disabled', false);
            }
        });
        $(".behalf_params input").prop('disabled', true);
    });
</script>