{include file='components/header_new.tpl' active='show_post'}

{if $contest_id == 2}
    {include file='contest_marketo/votes_counter_block.tpl' for_nominations_page=true}
{else}
    {include file='components/contest/votes_counter_block.tpl' for_nominations_page=true}
{/if}

<div class="page_sizer_wide">
    {if $contest_id <> 2}
        <div>
            {foreach from=$contest_categories_with_all item=category}
                <a class="post_type_link fleft contest_all_cat {if $selected_category==$category.tag_id}active{/if}" href="{$category.my_url}">{$category.name}</a>
            {/foreach}
            <div class="clear"></div>
        </div>
    {/if}
    <div class="nominations_div">
        <div class="more_container"
             data-offsetForMore="{$posts_on_page}"
             data-noMore="{$no_more_content}"
             data-pageType="contest_all"
             data-entityID="{$selected_category}">
            {if $content}
                {foreach from=$content item=front_post}
                    {include file='components/contest/nomination_post.tpl' post=$front_post}
                {/foreach}
            {else}
                No posts yet
            {/if}
        </div>
        <div class="clear"></div>
        <div class="front_loader_div hide"><img src="/images/loading.gif"></div>
    </div>

</div>
{include file='components/front/share_link_popup.tpl'}
{include file='components/js_common.tpl'}
<script>
    var contest_id = {$contest_id};

    $(document).ready(function() {

        preparePageForMore();
        prepareContentFilters();
        prepareContentDelete();
        prepareSharing();
        prepareVoting();
        prepareContestVoting();
    });

    function processErrors(errors) {
        if (errors) {
            $("#app_error_div").show();
        }

        $(".join_field_status_img").attr('src', '/images/input_ok.png').attr('title', '').show();

        for (var key in errors) {
            var name = errors[key]['name'];
            $("#contest_" + name).find(".join_field_status_img")
                    .attr('src', '/images/input_error.png')
                    .attr('title', errors[key]['msg']);
        }

    }
</script>

{include file='components/footer_new.tpl'}