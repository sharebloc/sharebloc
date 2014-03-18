{if isset( $smarty.session.modal_popups.ie_pop ) && $smarty.session.modal_popups.ie_pop == 1}
<div class="tcenter " id="ie_pop">
    <div class="popup">
        <div class="title_wide_popup">
            Consider a different browser?
        </div>
        <div class="content_block_popup_ie">
            To get the best experience, ShareBloc is optimized for the latest vesrsions of
                <a target="_blank" href="https://www.google.com/intl/en/chrome/browser/">Chrome</a>,
                <a target="_blank" href="http://support.apple.com/kb/dl1531">Safari</a> or
                <a target="_blank" href="http://www.mozilla.org/en-US/firefox/new/">Firefox</a>.
        </div>
        <div class="popup_function">
            <a id="ie_continue" class="cancel" href="javascript:void(0)">Continue</a>
        </div>
    </div>
</div>

<script language="JavaScript" type="text/javascript">
    $("#ie_continue").click(function(){
        $("#ie_pop").addClass("hide");
    });
</script>
{/if}