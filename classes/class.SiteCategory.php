<?php

class SiteCategory {

    private $entity_type;
    private $category_list;
    private $category_parents;

    function SiteCategory($entity_type = 'vendor') {
        $this->entity_type = $entity_type;
        $order_by = array('tag_name ASC');
        if ($entity_type=='contest') {
            $order_by = array('tag_id ASC');
        }

        $category_list = new GenericList('tag', null, array('tag_type' => $entity_type), null, null, null, $order_by, null, null, null);

        $this->category_list = $category_list->get();

        foreach ($this->category_list AS $category) {
            $this->category_parents[($category['parent_tag_id'] == 0 ? $category['tag_id'] : $category['parent_tag_id'])][] = $category['tag_id'];
        }
    }

    function get_category_parents() {
        return $this->category_parents;
    }

    function get_category_list() {
        return $this->category_list;
    }

    function get_lists() {
        $lists = array();

        foreach ($this->category_parents AS $category_id => $more_ids) {
            $joins                 = array();
            $joins[]               = array('type'   => 'inner', 'table'  => 'tag_selection', 'fields' => null, 'on'     => array('entity_id'   => $this->entity_type . '_id', 'entity_type' => '"' . $this->entity_type . '"'), 'where'  => array('tag_id' => $more_ids));
            $current_list          = new GenericList($this->entity_type, $joins, null, null, null, null, array('rand()'), 9, 0, null);
            $lists[$category_id] = $current_list->get();
        }

        return $lists;
    }

    function get_vendor_count() {
        global $db;

        $result = $db->select('vendor', array('count(*) AS count'));

        return $result[0]['count'];
    }

    function get_company_count() {
        global $db;

        $result = $db->select('company', array('count(*) AS count'));

        return $result[0]['count'];
    }

    function get_use_count() {
        global $db;

        $result = $db->select('link', array('count(*) AS count'), array(), array('link_type' => 'use'));

        return $result[0]['count'];
    }

}

?>