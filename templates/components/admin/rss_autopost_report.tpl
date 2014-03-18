<h2>Report on RSS auto-post script</h2>

<h3>Feeds parsed: {$feeds|count}</h3>

<h3>Details:</h3>
<div>Posts limit: {$crawl_limit}</div>
{if !$publish_posts}
    <div>Test mode: will <b>NOT</b> publish posts</div>
{/if}
<div>
    {foreach $feeds item=feed}
        <div>
            <h4>Feed by {$feed.entity_type} {$feed.name} (id {$feed.entity_id}), <a href='{$feed.url}' target='_blank'>{$feed.url}</a>:</h4>
            {if !$feed.allowed}
                <h4>SKIPPED - NOT IN LIST</h4>
            {/if}
            {if $feed.is_first_run}
                <h4>First run of script for this entity, will parse only first post</h4>
            {/if}
            <div>Posts found: {$feed.posts|count}, published: {$feed.published|count}, duplicates: {$feed.skipped|count}</div>
            {if $feed.published}
                <br>
                <div>Published posts:</div>
                <br>
                {foreach $feed.published item=post}
                    <div>
                        <span>
                            {if !empty($post.logo_url_thumb) || !empty($post.image)}
                                <img style="vertical-align: middle; margin: 10px 5px; width:50px; height:50px;"
                                     {if !empty($post.logo_url_thumb)}
                                        src="{$base_url}{$post.logo_url_thumb}"
                                    {else}
                                        src="{$post.image}"
                                    {/if}
                                     >
                            {/if}
                            {if $publish_posts}
                                <a href="{$base_url}{$post.my_url}" target="_blank">{$post.title}</a>, <a href="{$post.url}" target="_blank">source</a>
                            {else}
                                <a href="{$post.url}" target="_blank">{$post.title}</a>{if $post.image}, logo <a href="{$post.image}" target="_blank">image source</a>{/if} (TEST mode - not published)
                            {/if}
                        </span>
                    </div>
                {/foreach}
            {/if}
            {if $feed.skipped}
                <br>
                <div>Duplicates:</div>
                <br>
                {foreach $feed.skipped item=post}
                    <div>
                        (duplicate - skipped) <a href="{$base_url}{$post.my_url}" target="_blank">{$post.title}</a>, <a href="{$post.url}" target="_blank">source</a>
                    </div>
                {/foreach}
            {/if}
            {if $feed.errors}
                <br>
                <div>Errors:</div>
                {foreach $feed.errors item=error}
                    <div>{$error}</div>
                {/foreach}
            {/if}
        </div>
        <br>
        <hr>
    {/foreach}
</div>