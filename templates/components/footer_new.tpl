{if empty($f_iframe)}
    {include file='components/footer_links.tpl'}
    {if $show_join_widget && !$logged_in}
        {include file='components/join_widget.tpl'}
    {/if}
    {include file='components/ie_popup.tpl'}
{/if}
</body>
</html>
