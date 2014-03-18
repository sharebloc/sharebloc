<?php

class GenericList {

    protected $data;
    protected $pagination_data;
    protected $selections;
    protected $custom_query;
    protected $run_functions;
    protected $class_name;
    protected $primary_key;
    protected $sort_by;
    protected $sort_field;
    private $cache_key;
    private $errors;

    function GenericList($class_name, $join = null, $where = null, $not_where = array(), $group_by = array(), $having = array(), $order_by = '', $limit = 0, $offset = 0, $index_by = '') {
        global $cache;

        if (!isset($class_name)) {
            return false;
        }

        if (Settings::DEV_MODE) {
            if (!$limit) {
                $limit = 200;
            }
        }

        $this->class_name = $class_name;

        $sample_item      = new $this->class_name;
        $this->sort_field = $sample_item->get_entity_name();

        if (isset($this->custom_query) && !empty($this->custom_query)) {
            $cache_key       = $this->custom_query;
            $this->cache_key = $cache_key;
        } else {
            $cache_key       = array($class_name, $join, $where, $not_where, $group_by, $having, $order_by, $limit, $offset, $index_by);
            $this->cache_key = $cache_key;
        }

        $cache_result = $cache->get(get_class($this), $cache_key);

        if ($cache_result) {
            $this->data = $cache_result;
        } else {
            $this->load($join, $where, $not_where, $group_by, $having, $order_by, $limit, $offset, $index_by);
        }

        if (empty($cache_result)) {
            $cache->set(get_class($this), $cache_key, $this->data, 86400, $class_name);
        }
    }

    function load($join = null, $where = null, $not_where = array(), $group_by = array(), $having = array(), $order_by = '', $limit = 0, $offset = 0, $index_by = '') {
        global $db;

        if (!is_array($join) || count($join) == 0) {
            $join = array();
        }

        if (!is_array($where) || count($where) == 0) {
            $where = array();
        }

        if (!is_array($not_where) || count($not_where) == 0) {
            $not_where = array();
        }

        if (!is_numeric($limit) || $limit < 1) {
            $limit = 0;
        }

        if (!is_numeric($offset) || $offset < 1) {
            $offset = 0;
        }

        $sample_item = new $this->class_name;

        if ($this->custom_query) {
            $result = $db->query($this->custom_query, 0, $sample_item->get_primary_key());
        } else {
            $result = $db->select($sample_item->get_table_name(), array($sample_item->get_primary_key()), $join, $where, $not_where, $group_by, $having, $order_by, $limit, $offset, $sample_item->get_primary_key());
        }

        if (count($result) > 0) {
            foreach ($result AS $key => $val) {
                $obj = new $this->class_name($key);

                if (is_array($this->run_functions) && count($this->run_functions) > 0) {
                    foreach ($this->run_functions AS $func) {
                        call_user_func(array($obj, $func));
                    }
                }

                $this->data[$key] = $obj->get();

                if (is_array($val)) {
                    foreach ($val AS $k => $m) {
                        $this->data[$key][$k] = $m;
                    }
                }
            }

            return true;
        } else {
            return false;
        }
    }

    function get_limited($fields = array()) {
        $limited_results = array();

        foreach ($this->data AS $primary_key => $item) {
            foreach ($item AS $k => $v) {
                if (in_array($k, $fields)) {
                    $limited_results[$primary_key][$k] = $v;

                    $limited_results[$primary_key] = sort_array_by_array($limited_results[$primary_key], $fields);
                }
            }
        }

        return $limited_results;
    }

    function set_selection_criteria($selection_table = '', $where = array(), $selection_key = '') {
        if (!$selection_table)
            return false;

        $this->selection_criteria = array('table' => $selection_table, 'where' => $where, 'key'   => $selection_key);

        return true;
    }

    function set_custom_query($custom_query) {
        $this->custom_query = $custom_query;
    }

    function set_containing_entity($entity_type, $entity_id) {
        global $cache;
        $cache->set_additional_cache_key(get_class($this), $this->cache_key, $entity_type, $entity_id);
    }

    function load_selections() {
        global $db;

        $this->clear_selections();

        $result = $db->select($this->selection_criteria['table'], array($this->primary_key), null, $this->selection_criteria['where']);

        if (count($result) > 0) {
            foreach ($result AS $res) {
                $this->selections[] = $res[$this->primary_key];
            }

            return true;
        } else {
            return false;
        }
    }

    function clear_selections() {
        $this->selections = array();
    }

    function set_selections($selections) {
        $this->selections = array();

        foreach ($selections AS $key => $val) {
            $this->selections[] = $val;
        }
    }

    function save_selections() {
        global $db, $cache;

        /* Jun 13, 2013 modified by katya@vendorstack.com - https://vendorstack.atlassian.net/browse/VEN-104 */
        if (!isset($this->selections) || !is_array($this->selections)) {
            return false;
        }

        foreach ($this->selections AS $key => $value) {
            $insert_data                                     = $this->selection_criteria['where'];
            $insert_data[$this->selection_criteria['key']] = $value;

            $db->insert_on_duplicate_key_update($this->selection_criteria['table'], $insert_data);
        }

        if (count($this->selections) > 0)
            $not_where = array($this->selection_criteria['key'] => $this->selections);
        else
            $not_where = null;

        $db->delete($this->selection_criteria['table'], $this->selection_criteria['where'], $not_where);

        $cache->clear(get_class($this), $this->cache_key);
    }

    function get() {
        // todo bear - this can return non-array values (null) when list is empty. So we can't use this data directly without checks now.
        return $this->data;
    }

    function get_options_list($key, $values) {
        $options_list = array();

        if (isset($this->data) && is_array($this->data) && count($this->data) > 0) {
            foreach ($this->data AS $k => $v) {
                foreach ($values AS $value) {
                    $options_list[$v[$key]][$value] = $v[$value];
                }
            }
        }

        return $options_list;
    }

    function get_selections_list() {
        return $this->selections;
    }

    function get_selections_list_details() {
        $selection_details = array();
        foreach ($this->selections AS $selection_id) {
            if (isset($this->data[$selection_id]))
                $selection_details[$selection_id] = $this->data[$selection_id];
        }

        return $selection_details;
    }

    function get_errors() {
        return $this->errors;
    }

    function get_fields() {
        global $db;

        $fields = array();

        $squery = "DESC " . $this->table_name;

        $sresult = $db->query($squery);

        foreach ($sresult AS $sres) {
            if (strpos($sres['Type'], 'varchar(') !== false || strpos($sres['Type'], 'char(') !== false || strpos($sres['Type'], 'text') !== false) {
                $fields[$sres['Field']]['type'] = "text";

                if (strpos($sres['Type'], '(') !== false) {
                    $max_length                         = parse_text_between($sres['Type'], '(', ')');
                    $fields[$sres['Field']]['length'] = array('min' => 0, 'max' => $max_length);
                } else {
                    $fields[$sres['Field']]['length'] = array('min' => 0, 'max' => 25000);
                }
            } else if (strpos($sres['Type'], 'tinyint(') !== false) {
                $fields[$sres['Field']]['type'] = "number";

                if (strpos($sres['Type'], 'unsigned') !== false)
                    $fields[$sres['Field']]['range'] = array('min' => 0, 'max' => 255);
                else
                    $fields[$sres['Field']]['range'] = array('min' => -128, 'max' => 127);
            }
            else if (strpos($sres['Type'], 'smallint(') !== false) {
                $fields[$sres['Field']]['type'] = "number";

                if (strpos($sres['Type'], 'unsigned') !== false)
                    $fields[$sres['Field']]['range'] = array('min' => 0, 'max' => 65535);
                else
                    $fields[$sres['Field']]['range'] = array('min' => -32768, 'max' => 32767);
            }
            else if (strpos($sres['Type'], 'mediumint(') !== false) {
                $fields[$sres['Field']]['type'] = "number";

                if (strpos($sres['Type'], 'unsigned') !== false)
                    $fields[$sres['Field']]['range'] = array('min' => 0, 'max' => 16777215);
                else
                    $fields[$sres['Field']]['range'] = array('min' => -8388608, 'max' => 8388607);
            }
            else if (strpos($sres['Type'], 'bigint(') !== false) {
                $fields[$sres['Field']]['type'] = "number";

                if (strpos($sres['Type'], 'unsigned') !== false)
                    $fields[$sres['Field']]['range'] = array('min' => 0, 'max' => 18446744073709551615);
                else
                    $fields[$sres['Field']]['range'] = array('min' => -9223372036854775808, 'max' => 9223372036854775807);
            }
            else if (strpos($sres['Type'], 'int(') !== false) {
                $fields[$sres['Field']]['type'] = "number";

                if (strpos($sres['Type'], 'unsigned') !== false)
                    $fields[$sres['Field']]['range'] = array('min' => 0, 'max' => 4294967295);
                else
                    $fields[$sres['Field']]['range'] = array('min' => -2147483648, 'max' => 2147483647);
            }
            else if (strpos($sres['Type'], 'datetime') !== false) {
                $fields[$sres['Field']]['type'] = "datetime";
            } else if (strpos($sres['Type'], 'date') !== false) {
                $fields[$sres['Field']]['type'] = "date";
            } else if (strpos($sres['Type'], 'enum') !== false) {
                $fields[$sres['Field']]['type'] = "selection";

                $opt_text                            = str_replace("enum('", "", substr($sres['Type'], 0, -2));
                $fields[$sres['Field']]['options'] = explode("','", $opt_text);
                $fields[$sres['Field']]['default'] = $sres['Default'];
            } else {
                $fields[$sres['Field']] = "unhandled!";
            }
        }

        return $fields;
    }

    function calculate_completion_percentage() {
        $sample_item = new $this->class_name;

        $fields = $sample_item->get_fields();

        $vals = array();

        foreach ($this->data AS $item_key => $item) {
            $total     = 0;
            $completed = 0;

            foreach (array_keys($fields) as $key) {
                if (!isset($item[$key])) {
                    continue;
                }
                $value = $item[$key];
                $total++;
                if (( is_numeric($value) && $value > 0 ) || ( is_string($value) && strlen($value) > 1 ) || is_array($value) && count($value) > 0) {
                    $completed++;
                }
            }

            $this->data[$item_key]['comp%'] = floor($completed * 100 / $total + 0.5);

            $vals[] = $this->data[$item_key]['comp%'];
        }

        $min  = min($vals);
        $max  = max($vals);
        $diff = $max - $min;
        if (!$diff) {
            $diff = 1;
        }

        foreach ($this->data AS $item_key => $item) {
            $this->data[$item_key]['comp%'] = floor(($this->data[$item_key]['comp%'] - $min) * 100 / $diff + 0.5);
        }
    }

    function sort($function_name = '') {
        $sort_func = '';
        if (!empty($function_name))
            $sort_func = $function_name;
        else if (!empty($this->sort_by))
            $sort_func = $this->get_sort_function_name();

        if (is_array($this->data) && count($this->data) > 0) {
            if (!empty($sort_func))
                uasort($this->data, $sort_func);
            else
                arsort($this->data);
        }

        $new_data = array();

        if (is_array($this->data) && count($this->data) > 0) {
            foreach ($this->data AS $key => $val) {
                // todo bear why do we add _??
                $new_data[strval($key) . "_"] = $val;
            }
        }

        $this->data = $new_data;
    }

    function set_sort($sort_by) {
        if (in_array($sort_by, array('most_viewed', 'highest_rated', 'most_used', 'alphabeticalaz', 'alphabeticalza', 'most_recent', 'relevance'))) {
            $this->sort_by = $sort_by;
        }
    }

    function set_sort_field($sort_field) {
        $this->sort_field = $sort_field;
    }

    function get_sort_function_name() {
        if ($this->sort_by == 'most_viewed')
            return array($this, 'sort_by_most_viewed');
        else if ($this->sort_by == 'highest_rated')
            return array($this, 'sort_by_highest_rated');
        else if ($this->sort_by == 'most_used')
            return array($this, 'sort_by_most_used');
        else if ($this->sort_by == 'alphabeticalaz')
            return array($this, 'sort_by_field');
        else if ($this->sort_by == 'alphabeticalza')
            return array($this, 'sort_by_field_rev');
        else if ($this->sort_by == 'most_recent')
            return array($this, 'sort_by_most_recent');
        else if ($this->sort_by == 'relevance')
            return array($this, 'sort_by_relevance');
    }

    function sort_by_most_viewed($a, $b) {
        if (!isset($a['stats']['uniques']) || !isset($b['stats']['uniques']))
            return 0;

        if ($a['stats']['uniques'] == $b['stats']['uniques']) {
            return 0;
        }
        return ($a['stats']['uniques'] > $b['stats']['uniques']) ? -1 : 1;
    }

    function sort_by_highest_rated($a, $b) {
        if (!isset($a['rating']['rating']) || !isset($b['rating']['rating']))
            return 0;

        if ($a['rating']['rating'] == $b['rating']['rating']) {
            return 0;
        }
        return ($a['rating']['rating'] > $b['rating']['rating']) ? -1 : 1;
    }

    function sort_by_most_used($a, $b) {
        if (!isset($a['user_count']) || !isset($b['user_count']))
            return 0;

        if ($a['user_count'] == $b['user_count']) {
            return 0;
        }
        return ($a['user_count'] > $b['user_count']) ? -1 : 1;
    }

    function sort_by_most_recent($a, $b) {
        if (!isset($a['date_added']) || !isset($b['date_added']))
            return 0;

        if ($a['date_added'] == $b['date_added']) {
            return 0;
        }
        return ($a['date_added'] > $b['date_added']) ? -1 : 1;
    }

    function sort_by_relevance($a, $b) {
        $is_a = 0;
        $is_b = 0;

        if (is_logged_in() && $a['user']['user_id'] == $_SESSION['user_info']['user_id']) {
            $is_a = 1;
        }

        if (is_logged_in() && $b['user']['user_id'] == $_SESSION['user_info']['user_id']) {
            $is_b = 1;
        }

        if ($is_a == $is_b) {
            if ($a['vote']['total'] == $b['vote']['total']) {
                return 0;
            }
            return ($a['vote']['total'] > $b['vote']['total']) ? -1 : 1;
        }
        return ($is_a > $is_b) ? -1 : 1;
    }

    function sort_by_field($a, $b) {
        if (!empty($this->sort_field))
            $field = $this->sort_field;
        else
            $field = $this->primary_key;

        if (!isset($a[$field]) || !isset($b[$field]))
            return 0;

        if (strtolower($a[$field]) == strtolower($b[$field])) {
            return 0;
        }
        return ( strtolower($a[$field]) < strtolower($b[$field]) ) ? -1 : 1;
    }

    function sort_by_field_rev($a, $b) {
        if (!empty($this->sort_field))
            $field = $this->sort_field;
        else
            $field = $this->primary_key;

        if (!isset($a[$field]) || !isset($b[$field]))
            return 0;

        if (strtolower($a[$field]) == strtolower($b[$field])) {
            return 0;
        }
        return ( strtolower($a[$field]) > strtolower($b[$field]) ) ? -1 : 1;
    }

    function get_pagination_data() {
        return $this->pagination_data;
    }

    function get_total_count() {
        global $db;

        $sample_item = new $this->class_name;
        $table_name  = $sample_item->get_table_name();

        $squery = "SELECT count(*) AS count FROM " . $table_name;

        $sresult = $db->query($squery);

        return $sresult[0]['count'];
    }

    function paginate($num_per_page, $current_page) {
        if (empty($this->data) || count($this->data) == 0 || $num_per_page == 0) {
            return false;
        }

        if ($current_page == 0)
            $current_page = 1;

        $total_items = count($this->data);

        $start_range = $num_per_page * ( $current_page - 1 );
        $end_range   = $num_per_page * ($current_page) - 1;
        $num_pages   = ceil($total_items / $num_per_page);

        if ($total_items < $num_per_page) {
            $start_range  = 0;
            $end_range    = $total_items;
            $current_page = 1;
            $num_pages    = 1;
        } else if ($start_range > $total_items || $end_range > $total_items) {
            $start_range  = ( $num_pages - 1 ) * $num_per_page;
            $end_range    = $total_items;
            $current_page = $num_pages;
        }

        $this->pagination_data['total_items']  = $total_items;
        $this->pagination_data['current_page'] = $current_page;
        $this->pagination_data['num_pages']    = $num_pages;

        $this->data = array_slice($this->data, $start_range, ($end_range - $start_range + 1), true);
    }
}

?>