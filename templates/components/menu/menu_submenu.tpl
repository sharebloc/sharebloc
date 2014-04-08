{if $logged_in}
    {include file='components/front/front_invite_popup.tpl'}
{/if}

<div class="menu_black">
    <div class="page_sizer_wide">
        <div class="centered">
                {if $logged_in}
                    <a href="/" class="{if $active_submenu=='home'}active{/if}">Home</a>
                    <a href="{$user_info.my_url}/connections" class="{if $active_submenu=='connections'}active{/if}">Connections</a>
                {else}
                    <a href="/" class="{if $active_submenu=='join'}active{/if}">Join</a>
                {/if}
                <a href="/blocs/" class="{if $active_submenu=='blocs'}active{/if}">Blocs</a>
                <a href="/guidelines/" class="{if $active_submenu=='guidelines'}active{/if}">Guidelines</a>
                {if $logged_in}
                    <a href="#" id="invites_link" class="{if $active_submenu=='invites'}active{/if}">Invites</a>
                    <a href="/content_marketing_nation" class="{if $active_submenu=='contest'}active{/if}">Contest</a>
                {/if}
        </div>
    </div>
</div>
<!-- AK Adding in bar for Paul Rosenberg -->
{if $show_contest_widget}
<div class="sub_header_announcement">
    <div class="announcement_block">
        <a href="http://knowledge.funnelholic.com/funnelholic_sales_summit_sharebloc/" class="announcement_link" target="_blank">
    Learn sales tactics that work in Funnelholic's Sales Summit on April 17 featuring Jill Konrath, Jill Rowley, Matt Heinz and Dan Waldschmidt
        </a>
    </div>    
</div>
{/if}
