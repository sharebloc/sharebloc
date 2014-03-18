{include file='components/header_new.tpl' hide_submenu='1'}

<div align="center">
    <div class="content_block_text">
        <div class="team_header">About</div>
        <div class="team_description">ShareBloc is backed by prominent angels including <a href="http://corporate.kabam.com/leadership/executive-team/kevin-chou/">Kevin Chou</a> and <a href="http://500.co/">500Startups</a>.  You can also find us on <a href="https://angel.co/vendorstack">AngelList</a>.</div>
        <div id="bios_container">
            {foreach from=$team item=member}
                <div class="bio_small">
                    <img class="portrait_circle" src="/images/{$member.portrait}">
                    <div class="bio_title_block">
                        <div class="bio_name">{$member.name}</div>
                        <div class="bio_title">{$member.title}</div>
                        <div class="bio_links">
                            {if $member.twitter}
                                <a href="{$member.twitter}"><img class="icon_team" src="/images/icons/twittericon.png"/></a>
                            {/if}
                            {if $member.linkedin}
                                <a href="{$member.linkedin}"><img class="icon_team" src="/images/icons/linkedinicon.png"/></a>
                            {/if}
                        </div>
                    </div>
                </div>
            {/foreach}
            <div class="clear"></div>
        </div>
    </div>
    <div class="bottom_filler">
    </div>
</div>

{include file='components/js_common.tpl'}
{include file='components/footer_new.tpl'}