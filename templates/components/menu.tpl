<div id="header" {if $show_beta_border}style="border-bottom: 2px solid #ff0000;"{/if}>
    <div id="sub_header">
        <h1><a href="{$index_page}"><img class="vslogo_header" alt="ShareBloc" src="/images/sharebloc_logo.png"></a></h1>

        {if $logged_in}
            {include file='components/menu/menu_user_menu.tpl'}
        {else}
            {include file='components/menu/menu_login_btns.tpl'}
        {/if}
    </div>
</div>
{if !empty($smarty.session.status_message)}
    {include file='components/menu/menu_system_msg_bar.tpl'}
{/if}