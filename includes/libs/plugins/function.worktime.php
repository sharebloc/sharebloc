<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.worktime.php
 * Type:     function
 * Name:     worktime
 * Purpose:  shows total script execution time. Main script should set "start_ts" param.* 
 * -------------------------------------------------------------
 */
function smarty_function_worktime($params, &$smarty)
{
    $result = '';
    if (!isset($params['times']) || !$params['times']) {
        return 0;
    }
    
    $first = true;
    $params['times']['smarty_display'] = microtime(true);
    $first_time = $last_time = reset($params['times']);
    foreach ($params['times'] as $name=>$value) {
        if ($first) {
            $first = false;
            continue;
        }
        $result .= sprintf ("%s: %d ms<br>\n", 
                                $name,
                                ($value - $last_time)*1000);
        $last_time = $value;
    }
    
    $result = sprintf ("<b>Total: %d ms</b><br>\n", 
                                ($last_time - $first_time)*1000) . $result;

    return $result;
}
?>