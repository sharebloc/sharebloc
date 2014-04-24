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
                <a href="/calendar/sales__marketing" class="{if $active_submenu=='calendar'}active{/if}">Calendar</a>
                <a href="/guidelines/" class="{if $active_submenu=='guidelines'}active{/if}">Guidelines</a>


                    <!-- <a href="#" id="invites_link" class="{if $active_submenu=='invites'}active{/if}">Invites</a>
                     <a href="/content_marketing_nation" class="{if $active_submenu=='contest'}active{/if}">Contest</a> -->
                
        </div>
    </div>
</div>
