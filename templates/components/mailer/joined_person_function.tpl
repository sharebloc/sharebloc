{function name=joinedPerson user=array()}
    <tr>
        <td style="padding: 5px 12px 5px 0;">
            {if $user.logo.my_url_thumb}
                <img src="{$base_url}{$user.logo.my_url_thumb}" alt="{$user.full_name|escape}" style="border-radius:50%;
                                                                                    width:40px;
                                                                                    height: 40px;"/>
            {/if}
        </td>
        <td style="padding: 5px 0 5px 0;">
            <h1 style="font:13px 'helvetica neue',helvetica,arial,sans-serif; margin:0">
                <a href="{$base_url}{$user.my_url}" style="font: bold 15px 'helvetica neue', helvetica,arial, sans-serif;
                                                            text-decoration:none">
                    {$user.full_name|escape}
                </a>
                {if empty($no_text)}
                    {if $user.joined && $user.followed}
                        just joined and followed you
                    {elseif $user.joined}
                        just joined
                    {elseif $user.followed}
                        followed you
                    {/if}
                {/if}
            </h1>
            <h2 style="font:bold 13px 'helvetica neue',helvetica,arial,sans-serif;margin:0">
                <a href="{$base_url}{$user.my_url}/follow_user" style="text-decoration:none;
                                                                        color:#005598;">
                    Follow
                </a>
            </h2>
        </td>
    </tr>
{/function}