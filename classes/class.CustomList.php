<?php

require_once('class.GenericList.php');

class CustomList extends GenericList {

    protected $custom_query;
    protected $run_functions;

    function CustomList($class_name, $custom_query, $run_functions = null) {
        global $cache;

        $this->custom_query  = $custom_query;
        $this->run_functions = $run_functions;

        parent::GenericList($class_name);
    }

}

?>