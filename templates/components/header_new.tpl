<!DOCTYPE html>
<html>
    <head>
        <title>{if !empty($seo.title)}{$seo.title|escape} - ShareBloc{else}ShareBloc - A community for professionals{/if}</title>

        {if $use_contest_vote}
            {if $contest_id == 1}
                <meta name="keywords" content="content marketing, sales, marketing, 2013, inbound"/>
                <meta name="description" content="The Top 50 Content Marketing Posts of 2013 voted by the community">
            {else}
                <meta name="keywords" content="content marketing, marketing automation, sales, marketing, marketo"/>
                <meta name="description" content=" The Content Marketing Nation Contest">
            {/if}
        {else}
            <meta name="keywords" content="{if !empty($seo.keywords)}{$seo.keywords|escape}{else}business content, content discovery, enterprise, b2b, SMB, small medium business, lead-gen{/if}"/>
            <meta name="description" content="{if !empty($seo.keywords)}{$seo.description|escape}{else}ShareBloc is a community of like-minded professionals who share, curate and discuss business content that matters.{/if}">
        {/if}
        <meta http-equiv="content-type" content="text/html;charset=utf-8" />
        <link rel="icon" href="/favicon.png" type="image/x-icon">
        <link rel="stylesheet" href="/css/v4.css" type="text/css" />
        <link rel="stylesheet" href="/css/redmond/jquery-ui-1.8.17.custom.css" type="text/css"/>
        <link rel="stylesheet" href="/css/validationEngine.jquery.css" type="text/css" />
        {if !empty($frontpage)}
            {* just to not include frontpage css to main css file *}
            <link rel="stylesheet" href="/css/frontpage.css" type="text/css" />
        {/if}
        {if $use_contest_vote}
            {if $contest_id == 1}
                <link rel="stylesheet" href="/css/contest.css" type="text/css" />
            {else}
                <link rel="stylesheet" href="/css/contest_marketo.css" type="text/css" />
            {/if}

        {/if}

        <script src="https://code.jquery.com/jquery-1.7.1.min.js"></script>

        {if $dev_mode}
            {include file='components/debug_panel.tpl'}
        {/if}
    </head>
    <body class="{if $body_font=='lucida'}lucida{elseif $body_font=='helvetica'}helvetica{/if}">
        {if empty($f_iframe)}
            {include file='components/menu.tpl'}
            {if !$use_contest_vote && empty($frontpage) && empty($hide_submenu)}
                {include file='components/menu/menu_submenu.tpl'}
            {/if}
        {/if}
