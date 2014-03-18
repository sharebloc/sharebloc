{if isset( $rating_input ) && $rating_input == true}

    <div id="{$bar_name}_star_hover_bar" style="display: inline;">
    {section name=rating start=1 loop=6 step=1}

        {if $rating_value > 0 && $smarty.section.rating.index <= $rating_value}
        <img {if !empty($bar_id)}barid="{$bar_id}"{/if} alt="{$smarty.section.rating.index}" id="{$bar_name}_star{$smarty.section.rating.index}" class="{$star_size}_star {$bar_name}_hover_star" src="/images/icons/star.png" />
        {else}
        <img {if !empty($bar_id)}barid="{$bar_id}"{/if} alt="{$smarty.section.rating.index}" id="{$bar_name}_star{$smarty.section.rating.index}" class="{$star_size}_star {$bar_name}_hover_star" src="/images/icons/star_off.png" />
        {/if}

    {/section}
    </div>

    {if $logged_in || !empty($invite_data) || !empty($allow_for_not_logged)}
    {literal}
    <script>

        var context_msg=new Array();
        context_msg[1]="Disappointed.";
        context_msg[2]="Not the best I've seen.";
        context_msg[3]="Could be better.";
        context_msg[4]="Pretty good. Would recommend.";
        context_msg[5]="Best I've ever used.";

        ${/literal}{$bar_name}{literal}_star_value = {/literal}{if isset($rating_value) && $rating_value > 0}{$rating_value}{else}0{/if}{literal};

        $(".{/literal}{$bar_name}{literal}_hover_star").mouseenter(
          function () {
            $star_num = $(this).attr('alt');
            for( $i=1; $i <= 5; $i++ )
            {
                if( $i <= $star_num )
                {
                    $("#{/literal}{$bar_name}{literal}_star"+$i).attr('src','/images/icons/star.png');
                }
                else
                {
                    $("#{/literal}{$bar_name}{literal}_star"+$i).attr('src','/images/icons/star_off.png');
                }
            }

            {/literal}
            {if isset( $context_div )}



            $("#{$context_div}").html(context_msg[$star_num]);

            {/if}
            {literal}

          }
        );

        $("#{/literal}{$bar_name}{literal}_star_hover_bar").mouseleave(
          function ()
          {
            for( $i=1; $i <= 5; $i++ )
            {
                if ( $i <= ${/literal}{$bar_name}{literal}_star_value )
                    $("#{/literal}{$bar_name}{literal}_star"+$i).attr('src','/images/icons/star.png');
                else
                    $("#{/literal}{$bar_name}{literal}_star"+$i).attr('src','/images/icons/star_off.png');
            }

            {/literal}
            {if isset( $context_div )}
            $("#{$context_div}").html('');
            {/if}
            {literal}
          }
        );

    </script>
    {/literal}
    {/if}


{else}
    {if $rating_value > 0}
    {section name=rating start=1 loop=6 step=1}
      {if $smarty.section.rating.index <= $rating_value}
        <img class="{$star_size}_star" src="/images/icons/star.png" />
      {elseif $smarty.section.rating.index > $rating_value && $smarty.section.rating.index < $rating_value + 1}
        <img class="{$star_size}_star" src="/images/icons/star_half.png" />
      {elseif $smarty.section.rating.index > $rating_value}
        <img class="{$star_size}_star" src="/images/icons/star_off.png" />
      {/if}
    {/section}
    {else if isset($show_not_rated_text) && $show_not_rated_text == 1}
    No Rating Yet
    {/if}
{/if}