{if !$dev_mode}
<script type="text/javascript">
// AK 2014-04-14 note that literal is needed to make this work
 {literal} 
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-29473234-1', 'sharebloc.com');
  ga('send', 'pageview');

  function _gaLt(event){
    var el = event.srcElement || event.target;

    /* Loop up the tree through parent elements if clicked element is not a link (eg: an image in a link) */
    while(el && (typeof el.tagName == 'undefined' || el.tagName.toLowerCase() != 'a' || !el.href))
        el = el.parentNode;

    if(el && el.href){
        if(el.href.indexOf(location.host) == -1){ /* external link */
            ga("send", "event", "Outgoing Links", el.href, document.location.pathname + document.location.search);
            /* if target not set then delay opening of window by 0.5s to allow tracking */
            if(!el.target || el.target.match(/^_(self|parent|top)$/i)){
                setTimeout(function(){
                    document.location.href = el.href;
                }.bind(el),500);
                /* Prevent standard click */
                event.preventDefault ? event.preventDefault() : event.returnValue = !1;
            }
        }

    }
}

/* Attach the event to all clicks in the document after page has loaded */
var w = window;
w.addEventListener ? w.addEventListener("load",function(){document.body.addEventListener("click",_gaLt,!1)},!1)
  : w.attachEvent && w.attachEvent("onload",function(){document.body.attachEvent("onclick",_gaLt)});

{/literal}

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
