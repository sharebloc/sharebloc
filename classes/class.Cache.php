<?php

class Cache {

    private $memcache;
    private $global_cache;
    private $keys;
    private $keys_all;
    private $cache_limit;
    private $stats;
    private $auto_recache_keys = array(
        'vendor_id'  => 'Vendor',
        'company_id' => 'Company',
        'user_id'    => 'User'
    );

    function Cache() {
        $this->memcache = new Memcache;

        $this->memcache->connect('localhost', Settings::CACHE_MEMCACHE_PORT) or die("Could not connect");
        $this->global_cache = array();
        $this->keys         = array();
        $this->keys_all     = array();

        $this->stats = array('cache_hit_global'   => 0, 'cache_hit_memcache' => 0, 'no_key'             => 0,
            'cache_miss'         => 0, 'cache_set'          => 0, 'skip_cache'         => 0, 'delete_key'         => 0,
            'delete_real_key'    => 0, 'delete_all'         => 0, 'delete_all_key'     => 0);


        $this->cache_limit = 0;
        if (defined("Settings::CACHE_MEMCACHE_LIMIT")) {
            $this->cache_limit = Settings::CACHE_MEMCACHE_LIMIT;
        }
    }

    function get($class_name, $key) {
        $real_key = $this->generate_key($class_name, $key);

        if (!$real_key) {
            $this->stats['no_key']++;
            return false;
        } else if (!empty($this->global_cache[$real_key])) {
            $this->stats['cache_hit_global']++;

            if (isset($this->keys_all[$real_key]))
                $this->keys_all[$real_key]++;
            else
                $this->keys_all[$real_key] = 1;

            return unserialize($this->global_cache[$real_key]);
        }
        else if (!defined('_NO_MEMCACHE') && ( $mem_result = $this->memcache->get($real_key) )) {
            if (!$mem_result || $mem_result == 'N;')
                return false;

            $this->stats['cache_hit_memcache']++;

            if (isset($this->keys_all[$real_key]))
                $this->keys_all[$real_key]++;
            else
                $this->keys_all[$real_key] = 1;

            return unserialize($mem_result);
        }
        else {
            $this->stats['cache_miss']++;
            return false;
        }
    }

    function set($class_name, $key = '', $val = '', $timeout = 86400, $list_type = null) {
        global $db;

        $real_key = $this->generate_key($class_name, $key);

        $this->garbage_collection();

        $serialized = serialize($val);

        $this->global_cache[$real_key] = $serialized;
        $this->memcache->set($real_key, $serialized, MEMCACHE_COMPRESSED, $timeout);

        $this->keys[$class_name][$real_key] = $real_key;

        if (!isset($this->keys_all[$real_key]))
            $this->keys_all[$real_key] = 1;

        if (is_array($val) && in_array($class_name, array('GenericList', 'CustomList'))) {
            foreach ($val AS $k => $item) {
                $iquery = "INSERT INTO recache ( entity_id, entity_type, key_value, date_added, date_modified ) VALUES " .
                        "( '" . $db->escape_text($k) . "', '" . $db->escape_text(strtolower($list_type)) . "', '" . $db->escape_text($real_key) . "', now(), now() ) " .
                        "ON DUPLICATE KEY UPDATE date_modified = now()";
                $db->query($iquery);
            }

            $iquery = "INSERT INTO recache ( entity_id, entity_type, key_value, key_full, date_added, date_modified ) VALUES " .
                    "( '" . $db->escape_text(0) . "', '" . $db->escape_text(strtolower($class_name)) . "', '" . $db->escape_text($real_key) . "', '" . $db->escape_text(serialize($key)) . "', now(), now() ) " .
                    "ON DUPLICATE KEY UPDATE date_modified = now()";
            $db->query($iquery);
        }

        $this->stats['cache_set']++;
    }

    function set_additional_cache_key($class_name, $key = '', $entity_type = '', $entity_key = '') {
        global $db;

        $real_key = $this->generate_key($class_name, $key);

        $iquery = "INSERT INTO recache ( entity_id, entity_type, key_value, date_added, date_modified ) VALUES " .
                "( '" . $db->escape_text($entity_key) . "', '" . $db->escape_text(strtolower($entity_type)) . "', '" . $db->escape_text($real_key) . "', now(), now() ) " .
                "ON DUPLICATE KEY UPDATE date_modified = now()";
        $db->query($iquery);
    }

    function clear($class_name, $key = null) {
        if (!$class_name)
            return false;

        if (!empty($key)) {
            $real_key = $this->generate_key($class_name, $key);

            unset($this->global_cache[$real_key]);
            $this->memcache->set($real_key, '', MEMCACHE_COMPRESSED, 0);
            $this->stats['delete_key']++;

            $this->clear_associated_lists($class_name, $key);
        } else {
            $this->stats['delete_all']++;

            if (isset($this->keys[$class_name]) && is_array($this->keys[$class_name])) {
                foreach ($this->keys[$class_name] AS $real_key) {
                    unset($this->global_cache[$real_key]);
                    $this->memcache->set($real_key, '', MEMCACHE_COMPRESSED, 0);
                    $this->stats['delete_all_key']++;
                }
            }
        }
    }

    function clear_real_key($real_key) {
        unset($this->global_cache[$real_key]);
        $this->memcache->set($real_key, '', MEMCACHE_COMPRESSED, 0);
        $this->stats['delete_real_key']++;
    }

    function garbage_collection() {
        if (!isset($this->keys_all) || $this->cache_limit == 0 || count($this->keys_all) < $this->cache_limit) {
            return false;
        }

        asort($this->keys_all);

        $new_keys  = array();
        $new_cache = array();

        $loop_ctr = 0;
        foreach ($this->keys_all AS $cache_key => $count) {
            if ($loop_ctr >= $this->cache_limit)
                break;

            $new_keys[$cache_key] = $count;

            if (isset($this->global_cache[$cache_key]))
                $new_cache[$cache_key] = $this->global_cache[$cache_key];

            $loop_ctr++;
        }

        unset($this->keys_all);
        unset($this->global_cache);

        $this->keys_all     = $new_keys;
        $this->global_cache = $new_cache;
    }

    function generate_key($class_name = '', $keys = null) {
        $key = $class_name;

        if (is_array($keys))
            $key .= serialize($keys);
        else
            $key .= $keys;

        return md5($key);
    }

    function get_stats() {
        return $this->stats;
    }

    function check_recache_keys($items) {
        foreach ($items AS $key => $value) {
            if (in_array($key, array_keys($this->auto_recache_keys))) {
                $this->clear_object_cache($this->auto_recache_keys[$key], $value);
            }
        }
    }

    function clear_object_cache($class_name, $primary_id = null, $secondary_id = null) {
        $class_file = DOCUMENT_ROOT . '/classes/class.' . $class_name . '.php';

        if (!file_exists($class_file))
            return false;

        require_once($class_file);

        if (!class_exists($class_name))
            return false;

        if (get_parent_class($class_name) != "BaseObject")
            return false;

        $obj = new $class_name($primary_id, $secondary_id);

        $obj->recache();
    }

    function clear_associated_lists($class_name, $key) {
        global $db;
        if ($class_name=='PostedLink') {
            // todo bear quick fix as it's a mess to define where cache key is set to table name but not to parameter passed to customlist
            $class_name='posted_link';
        }

        if (is_numeric($key)) {
            $squery = "SELECT DISTINCT key_value FROM recache WHERE entity_id = '" . $db->escape_text($key) . "' AND entity_type = '" . $db->escape_text(strtolower($class_name)) . "'";

            $sresult = $db->query($squery);

            foreach ($sresult AS $sres) {
                $this->clear_real_key($sres['key_value']);

                $dquery = "DELETE FROM recache WHERE key_value = '" . $db->escape_text($sres['key_value']) . "'";
                $db->query($dquery);
            }
        }
    }


    function getM($key) {
        if (defined('_NO_MEMCACHE')) {
            return false;
        }

        $result = $this->memcache->get($key);

        if (!$result || $result == 'N;') {
                return false;
        }

        return unserialize($result);
    }

    function setM($key, $val, $timeout = 86400) {
        if (defined('_NO_MEMCACHE')) {
            return false;
        }
        $serialized = serialize($val);
        $this->memcache->set($key, $serialized, MEMCACHE_COMPRESSED, $timeout);
    }

}

?>
