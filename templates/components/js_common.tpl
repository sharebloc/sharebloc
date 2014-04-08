{if !$dev_mode}
<script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-29473234-1']);
    _gaq.push(['_trackPageview']);

    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' === document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(ga, s);
    })();

    (function(){
        var uv=document.createElement('script');uv.type='text/javascript';
        uv.async=true;uv.src='//widget.uservoice.com/VVuwBNwb4IYi8DZ0hI8Q.js';
        var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(uv,s);
    })();

    {if empty($f_iframe)}
        UserVoice = window.UserVoice || [];
        UserVoice.push(['showTab', 'classic_widget',
        { mode: 'full', primary_color: '#cc6d00', link_color: '#007dbf', default_mode: 'support', forum_id: 175806,
            tab_label: 'Feedback', tab_color: '#005698',
            tab_position:{if empty($frontpage)}'middle-left'{else}'bottom-right'{/if},
            tab_inverted: false}
        ]);
    {/if}
</script>
{/if}

<script type="text/javascript">
    var is_admin = {if $is_admin}true{else}false{/if};
    var is_logged = {if $logged_in}true{else}false{/if};
    var is_voter = {if $contest_voter}true{else}false{/if};
    var is_elite = {if $is_elite}true{else}false{/if};
    var dev_mode = {if $dev_mode}true{else}false{/if};
    var login_url = "{$login_redir_path}";
    var alert_message = "{$alert_message}";
    {if $init_image_upload}
        var session_id = '{$session_id}';
    {/if}
    {if isset($tags_filter_json)}
        var tags_filter = {$tags_filter_json};
    {/if}
     var ajax_timeout = {if $dev_mode}150000{else}15000{/if};
     var use_contest_vote = {$use_contest_vote};
</script>

<script src="https://code.jquery.com/ui/1.8.17/jquery-ui.min.js"></script>
<script src="/js/languages/jquery.validationEngine-en.js"></script>
<script src="/js/jquery.validationEngine.js"></script>
{if $init_image_upload}
    <script src="/uploadify/swfobject.js"></script>
    <script src="/uploadify/jquery.uploadify.v2.1.4.min.js"></script>
{/if}
{if $init_clipboard_copy}
    <script type="text/javascript" src="/js/ZeroClipboard.min.js"></script>
{/if}

<script src="/js/utils.js"></script>

{if $dev_mode && $shouldUseCssRefresh}
    <script src="/js/css_refresh.js" type="text/javascript"></script>
{/if}
