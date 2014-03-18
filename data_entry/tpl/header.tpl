<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <base href="{$base_url}">
        <title>
        {if !empty($curr_page_title)}
            {$curr_page_title} -
        {elseif !empty($error_msg)}
            {$error_msg} -
        {/if}Data Entry Site
        </title>

        <script type="text/javascript" src="../js/jquery.min.js"></script>

        <!-- Bootstrap -->
        <link href="css/bootstrap.css" rel="stylesheet" media="screen">
        <link rel="stylesheet" type="text/css" href="css/data_entry.css" />
    </head>
<body>