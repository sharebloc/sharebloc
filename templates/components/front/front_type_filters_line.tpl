{if empty($filters_url)}
    {assign var="filters_url" value=$index_page}
{/if}
<div class="posts_filters_div">
    Show Me:
    <a class="posts_filter {if $type_filter=='all'}filter_active{/if}" href="{$filters_url}?type_filter=all">All</a>
    <a class="posts_filter {if $type_filter=='posted_link'}filter_active{/if}" href="{$filters_url}?type_filter=posted_link">Links</a>
    <a class="posts_filter {if $type_filter=='question'}filter_active{/if}" href="{$filters_url}?type_filter=question">Questions</a>
</div>