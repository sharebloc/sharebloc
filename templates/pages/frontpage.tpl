{assign var='frontpage' value=true}
{include file='components/header_new.tpl'}
<div class="block_container image_block_sb">
    <div class="block_content">
        <div class="image_content_sb">
            <div class="sb_block">
                <div class="sb_coming_title">Read business content<br>that matters to you.</div>
                <div class="sb_coming_descr">Get started. Registration takes less than 1 minute.</div>
                <div class="sb_form_div">
                    <form id="join_form" method="post" action="{$join_redir_path}" class="">
                        <input id="sb_email" name="join_sharebloc_email" class="sb_email_input" type="text" placeholder="Your Email"/><a id="sb_join_btn" class="sb_join_btn" href="javascript:void(0)">Join
                        </a>
                    </form>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>
<div class="lowerbar_content">
    Featured on:
    <div class="lower_links">
        <img src="images/techcrunch_gray.png">
        <img src="images/huffpo.png">
        <img src="images/smt.png">
        <img src="images/hubspot.png">
        <img src="images/marketo.png">
    </div>
</div>
{include file='components/js_common.tpl'}
<script type="text/javascript">
    $(document).ready(function() {
        $("#sb_join_btn").click(function() {
            $("#join_form").submit();
            return false;
        });
    });
</script>
{include file='components/footer_new.tpl'}
