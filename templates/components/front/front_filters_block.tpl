{* temporary solution to hide filters*}
{assign var=no_tags_filter value=true}

{if empty($filters_url)}
    {assign var="filters_url" value=$index_page}
{/if}
<div>
    <a class="post_type_link fleft {if $order=='rating'}active{/if}" href="{$filters_url}?order=rating">Top</a>
    <a class="post_type_link fleft {if $order=='date'}active{/if}" href="{$filters_url}?order=date">Recent</a>
    {if empty($no_tags_filter)}
        <a class="post_type_link fright {if $tags_filter_enabled}active{/if}"
           id="my_feed_link"
           href="{if $logged_in}{$filters_url}?tags_filter_enabled={if $tags_filter_enabled}0{else}1{/if}{else}#{/if}">
           {if $logged_in}{if $tags_filter_enabled}Filtering{else}Filter{/if}{else}Filter{/if}</a>
    {/if}
    <div class="clear"></div>
</div>

{if $tags_filter_enabled && empty($no_tags_filter)}
    <div class="front_tags_filters_container">
        {if $tags_filter}
            <div class="fleft front_your_filters_word">Your filters:</div>
            {foreach $tags_filter item=tag_id}
                <div class="mentioned_tag">
                    <div class="mentioned_tag_title">{$all_tags.$tag_id.tag_name}</div>
                    <div class="mentioned_tag_x mention_delete" data-tagID="{$tag_id}" href="javascript:void(0)">x</div>
                </div>
            {/foreach}
        {/if}
        {if $logged_in}
            <a id="edit_feed_lnk" class="{if !$tags_filter}lonely{/if}" href="#">+ Filter</a>
        {/if}
        <div class="clear"></div>
    </div>
    <div id="filter_popup_container" class="standard_popup_container hide">
        <div class="standard_popup popup_front_filter">
            <div id="filter_title" class="title_wide_popup">{if $logged_in}Filter{else}My Feed{/if}</div>
            <div class="standard_popup_content">
                <form id="tags_filter_form" action="{$filters_url}" method="post">
                    <input type="hidden" id="tags_filter_update" name="tags_filter_update" value="1">
                    <input type="hidden" id="remove_tag_from_filter" name="remove_tag_from_filter" value="0">
                    <input type="hidden" name="redirect_to_get" value="1">
                    {foreach from=$categories key=tag_id item=tag}
                        <div class="tags_filter_line">
                            <input id="category_chk_{$tag_id}" type="checkbox" class="category_chk" data-tagId="{$tag_id}" name="tags_filter[{$tag_id}]">
                            <label for="category_chk_{$tag_id}">{$all_tags.$tag_id.tag_name}</label>
                        </div>
                    {/foreach}
                </form>
            </div>
            <div class="popup_function">
                <a id="filter_cancel" class="cancel" href="#">Cancel</a>
                <a id="filter_save" class="save_changes" href="{if !$logged_in}{$login_redir_path}{/if}">Save{if !$logged_in} Filter{/if}</a>
            </div>
        </div>
    </div>
{/if}