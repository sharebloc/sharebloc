    <div class="follow_block follow_type_{$follow.follow_type} follow_entity_{$follow.entity_type} {if !empty($no_image_follows)}no_image{/if}" data-followerUid='{$follow.entity_uid}'>
        {if empty($no_image_follows) && $follow.logo.my_url}
            <a href="{$follow.my_url}" {if isset($target_blank)}target="_blank"{/if}>
                <img class="follow_logo" src="{if $follow.logo.my_url}{$follow.logo.my_url}{/if}">
            </a>
        {/if}
        <div class="follow_details">
            <div class="follow_name"><a href="{$follow.my_url}" {if isset($target_blank)}target="_blank"{/if}>{$follow.name|escape|truncate:50:"...":true}</a></div>
            {if $follow.related}
            <div class="follow_related_name"><a href="{$follow.related.my_url}" {if isset($target_blank)}target="_blank"{/if}>{$follow.related.name|escape|truncate:50:"...":true}</a></div>
            {/if}
        </div>
        {if !$logged_in || $follow.entity_uid!=$user_info.uid}
            <div class="action_button profile_follow {if $follow.followed_by_curr_user}active{/if}"
                 data-whomType='{$follow.entity_type}'
                 data-uid='{if $logged_in}{$user_info.uid}{/if}'
                 data-whomId='{$follow.entity_id}'>{if $follow.followed_by_curr_user}Following{else}Follow{/if}</div>
        {/if}
    </div>
