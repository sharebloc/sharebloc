<?php

class Database {

    var $db_connection = '';
    var $affected_rows;
    var $returned_rows;
    var $insert_id;

    function Database() {
        global $query_count;
        $query_count = 0;
    }

    function connect() {
        if (!$this->db_connection) {
            $this->db_connection = mysql_connect(Settings::DB_HOST, Settings::DB_USER, Settings::DB_PASSWORD) or Log::$logger->fatal("Failed to connect to database: " . Settings::DB_HOST . " - " . mysql_error());
            mysql_select_db(Settings::DB_DBNAME, $this->db_connection) or Log::$logger->fatal("Failed to select database: " . Settings::DB_DBNAME . " - " . mysql_error());
        }
    }

    function query($query_string, $cache_length = 0, $array_index = '') {
        global $query_count;
        $this->connect();

        if (substr(strtolower(trim($query_string)), 0, 6) == "select" || substr(strtolower(trim($query_string)), 0, 4) == "desc")
            $query_type = 0;
        else
            $query_type = 1;

        $result = mysql_query($query_string, $this->db_connection) or Log::$logger->error("MySQL Query Failed: \n\n" . $query_string . "\n\n - " . mysql_error());
        $query_count++;

        Log::$logger->trace("Will execute query: $query_string");

        if ($mysql_error = mysql_error($this->db_connection)) {
            Log::$logger->fatal("Error found: $mysql_error");
            exit();
        }

        if ($query_type == 1) {
            $this->affected_rows = mysql_affected_rows($this->db_connection);
            $this->insert_id     = mysql_insert_id($this->db_connection);

            if ($this->affected_rows > -1)
                return true;
            else
                return false;
        }
        else {
            $result_arr = array();

            if (@mysql_num_rows($result) > 0) {
                $this->returned_rows = mysql_num_rows($result);

                while ($item = mysql_fetch_assoc($result)) {
                    if ($array_index == '') {
                        $result_arr[]                      = $item;
                    } else {
                        if (!isset($item[$array_index])) {
                            Log::$logger->error("No key in array in DB query, sql = $query_string");
                        } else {
                            $result_arr[$item[$array_index]] = $item;
                        }
                    }
                }
            }
            return $result_arr;
        }
    }

    // added by bear, should be used in all new select queries
    static function execArray($sql, $one_row = false) {
        global $db;

        $result = $db->query($sql);
        if (!$result) {
            return array();
        }

        if ($one_row) {
            return $result[0];
        }

        return $result;
    }

    // added by bear, should be used in all new exec queries. Is a wrapper to not use global $db variable everywhere
    static function exec($sql) {
        global $db;
        return $db->query($sql);
    }

    // added by bear, should be used in all new exec queries. Is a wrapper to not use global $db variable everywhere
    static function escapeString($string) {
        global $db;
        return $db->escape_text($string);
    }

    function escape_text($text) {
        $this->connect();

        return mysql_real_escape_string($text, $this->db_connection);
    }

    // added by bear@deepshiftlabs.com - as we are using the deprecated "mysql" driver, it does not support the prepare().
    // but it is too expensive to call the escape_text() for several parameters in any INSERT/UPDATE query, so this is a fast version of it.
    // Queries using this should be rewrited to using prepare() when we will move to the mysqli driver.
    // WARN does not handles things like '\"' (will be converted to '"' by mysql).
    function escape_text_local($text) {
        return str_replace("'", "''", $text);
    }

    function quote_text($text) {
        $unquoted_strings = array("now()", "curdate()", "null");

        if (in_array(strtolower($text), $unquoted_strings)) {
            return $text;
        }
        else
            return "'" . $text . "'";
    }

    function escape_and_quote(&$item1) {
        if (is_array($item1)) {
            foreach ($item1 AS $key => $value) {
                $item1[$key] = $this->escape_text($value);
                $item1[$key] = $this->quote_text($value);
            }
        } else {
            $item1 = $this->escape_text($item1);
            $item1 = $this->quote_text($item1);
        }
    }

    function select($table, $fields = array(), $joins = array(), $where = array(), $not_where = array(), $group_by = array(), $having = array(), $order_by = '', $limit = 0, $offset = 0, $index_by = '') {
        if (count($where) > 0)
            array_walk($where, array($this, 'escape_and_quote'));
        if (count($not_where) > 0)
            array_walk($not_where, array($this, 'escape_and_quote'));
        if (count($having) > 0)
            array_walk($having, array($this, 'escape_and_quote'));

        if (count($fields) == 0) {
            $fields[] = "*";
        } else {
            foreach ($fields AS $k => $v) {
                if (strpos($v, ".") === false && strpos(strtolower($v), "count(") === false && strpos(strtolower($v), "sum(") === false)
                    $fields[$k] = "t1." . $v;
            }
        }

        $join_clause = "";
        $join_where  = "";

        $table_num = 2;
        if (count($joins) > 0) {
            foreach ($joins AS $join_info) {
                if (isset($join_info['type']) && strtolower($join_info['type']) == "left")
                    $join_clause .= " LEFT JOIN ";
                else
                    $join_clause .= " INNER JOIN ";

                $join_clause .= $join_info['table'] . " t" . $table_num . " ON ";

                if (isset($join_info['fields'])) {
                    foreach ($join_info['fields'] AS $field) {
                        $fields[] = "t" . $table_num . ".$field";
                    }
                }

                foreach ($join_info['on'] AS $key => $value) {
                    if ($this->has_quotes($value))
                        $join_clause .= "t" . $table_num . ".$key = $value AND ";
                    else
                        $join_clause .= "t" . $table_num . ".$key = t1.$value AND ";
                }

                $join_clause = substr($join_clause, 0, -4);

                if (isset($join_info['where']) && count($join_info['where']) > 0) {
                    foreach ($join_info['where'] AS $key => $value) {
                        if (!is_array($value) && ( substr($value, 1, 1) == '%' || substr($value, -2, 1) == '%' ))
                            $join_where .= "t" . $table_num . ".$key LIKE $value AND ";
                        else if (is_array($value))
                            $join_where .= "t" . $table_num . ".$key IN ( " . implode(',', $value) . ") AND ";
                        else
                            $join_where .= "t" . $table_num . ".$key = $value AND ";
                    }
                }

                $table_num++;
            }

            $join_where = substr($join_where, 0, -4);
        }

        $squery = "SELECT " . implode(", ", $fields) . " FROM $table t1 ";

        $squery .= $join_clause;

        if (count($where) > 0) {
            $squery .= " WHERE ";

            $squery .= $this->prepare_where_text($where);
        }

        if ($join_where) {
            if (count($where) == 0)
                $squery .= " WHERE ";
            else
                $squery .= " AND ";

            $squery .= $join_where;
        }

        if (count($not_where) > 0) {
            if (count($where) == 0 && $join_where == '')
                $squery .= " WHERE ";
            else
                $squery .= " AND ";

            foreach ($not_where AS $key => $value) {
                if (substr($value, 1, 1) == '%' || substr($value, -2, 1) == '%')
                    $squery .= "$key NOT LIKE $value AND ";
                else
                    $squery .= "$key <> $value AND ";
            }
            $squery = substr($squery, 0, -4);
        }

        if (count($group_by) > 0) {
            $squery .= " GROUP BY " . implode(", ", $group_by) . " ";
        }

        if (count($having) > 0) {
            $squery .= " HAVING ";
            foreach ($having AS $key => $value) {
                $squery .= "$key = $value AND ";
            }
            $squery = substr($squery, 0, -4);
        }

        if (!empty($order_by)) {
            if (is_array($order_by) && count($order_by) > 0) {
                $squery .= " ORDER BY ";

                foreach ($order_by AS $o) {
                    if (strpos(strtolower($o), "rand()") !== false || strpos(strtolower($o), "count(") !== false)
                        $squery .= $o . ", ";
                    else
                        $squery .= "t1." . $o . ", ";
                }

                $squery = substr($squery, 0, -2);
            }
            else if (strlen($order_by) > 0) {
                $squery .= " ORDER BY " . $order_by . " ";
            }
        }

        if ($offset > 0 && $limit > 0) {
            $squery .= " LIMIT $offset, $limit";
        } else if ($limit > 0) {
            $squery .= " LIMIT $limit";
        }

        $sresult = $this->query($squery, 0);

        $result = array();

        if (strlen($index_by) > 0 && ( $fields[0] == "*" || ( $fields[0] != "*" && in_array('t1.' . $index_by, $fields) ) )) {
            if (count($sresult) > 0) {
                foreach ($sresult AS $sres) {
                    $result[$sres[$index_by]] = $sres;
                }
            }
        } else {
            $result = $sresult;
        }

        return $result;
    }

    function prepare_where_text($where_array, $table_prefix = 't1') {
        $query_text = "";

        if (is_array($where_array) && count($where_array) > 0) {
            foreach ($where_array AS $key => $value) {
                if (!is_array($value) && ( substr($value, 1, 1) == '%' || substr($value, -2, 1) == '%' ))
                    $query_text .= ( $this->has_prepended_table_name($key) ? '' : $table_prefix . '.' ) . "$key LIKE $value AND ";
                else if (is_array($value))
                    $query_text .= ( $this->has_prepended_table_name($key) ? '' : $table_prefix . '.' ) . "$key IN ( " . implode(",", $value) . ") AND ";
                else
                    $query_text .= ( $this->has_prepended_table_name($key) ? '' : $table_prefix . '.' ) . "$key = $value AND ";
            }

            $query_text = substr($query_text, 0, -4);
        }

        return $query_text;
    }

    function has_prepended_table_name($key_name) {
        if (strpos($key_name, ".") !== false) {
            return true;
        } else {
            return false;
        }
    }

    function insert_on_duplicate_key_update($table, $data, $no_update_fields = array(), $update_additional_fields = array()) {
        // todo bear This should be completely rewritten.
        // This array_walk does empty strings from nulls and strings from integers.
        array_walk($data, array($this, 'escape_and_quote'));

        $iquery = "INSERT INTO $table ( " . implode(", ", array_keys($data)) . " ) " .
                "VALUES ( " . implode(", ", $data) . " ) ";

        $iquery .= "ON DUPLICATE KEY UPDATE ";
        foreach ($data AS $key => $value) {
            if (!in_array($key, $no_update_fields) && !in_array($key, array_keys($update_additional_fields)))
                $iquery .= "$key = $value, ";
        }

        if (count($update_additional_fields) > 0) {
            foreach ($update_additional_fields AS $key => $value) {
                $iquery .= "$key = $value, ";
            }
        }

        $iquery = substr($iquery, 0, -2);

        $this->query($iquery, 0);
    }

    function insert($table, $data, $ignore = false) {
        $add_ignore = "";
        if ($ignore == true)
            $add_ignore = " IGNORE ";

        array_walk($data, array($this, 'escape_and_quote'));

        $iquery = "INSERT $add_ignore INTO $table ( " . implode(", ", array_keys($data)) . " ) " .
                "VALUES ( " . implode(", ", $data) . " ) ";

        $this->query($iquery, 0);
    }

    function update($table, $data, $where, $limit = 0) {
        array_walk($data, array($this, 'escape_and_quote'));
        array_walk($where, array($this, 'escape_and_quote'));

        $uquery = "UPDATE $table SET ";

        foreach ($data AS $key => $value) {
            $uquery .= "$key = $value, ";
        }
        $uquery = substr($uquery, 0, -2);

        $uquery .= " WHERE 1 ";

        foreach ($where AS $key => $value) {
            $uquery .= "AND $key = $value ";
        }
        $uquery = substr($uquery, 0, -1);

        if ($limit > 0) {
            $uquery .= " LIMIT $limit";
        }

        $this->query($uquery, 0);
    }

    function delete($table, $where = array(), $not_where = array(), $limit = false) {
        if (count($where) > 0)
            array_walk($where, array($this, 'escape_and_quote'));

        if (count($not_where) > 0)
            array_walk($not_where, array($this, 'escape_and_quote'));

        $dquery = "DELETE FROM $table ";

        $dquery .= " WHERE 1 ";

        if (count($where) > 0) {
            foreach ($where AS $key => $value) {
                if (is_array($value))
                    $dquery .= " AND $key IN ( " . implode(",", $value) . " ) ";
                else
                    $dquery .= " AND $key = $value ";
            }
        }

        if (count($not_where) > 0) {
            foreach ($not_where AS $key => $value) {
                if (is_array($value))
                    $dquery .= " AND $key NOT IN ( " . implode(",", $value) . " ) ";
                else
                    $dquery .= " AND $key <> $value ";
            }
        }

        if ($limit > 0) {
            $dquery .= " LIMIT $limit";
        }

        $this->query($dquery, 0);
    }

    function has_quotes($str) {
        if (substr($str, 0, 1) == "'" && substr($str, -1) == "'") {
            return true;
        } else if (substr($str, 0, 1) == '"' && substr($str, -1) == '"') {
            return true;
        } else {
            return false;
        }
    }

}

?>
