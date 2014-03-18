<?php

require_once('class.GenericList.php');

class TagList extends GenericList {

    protected $data;
    protected $fields;
    protected $primary_key   = 'tag_id';
    protected $secondary_key = 'tag_name';
    protected $table_name    = 'tag';

    function TagList($tag_type) {
        if (!in_array($tag_type, array('extended_vendor', 'vendor', 'vendor_platform', 'vendor_cost', 'industry', 'contest'))) {
            return false;
        }

        $apply_type = $tag_type;
        if ($tag_type =='extended_vendor') {
            $apply_type = 'vendor';
        }

        $joins = array();
        $where = array('tag_type' => $apply_type);

        parent::GenericList('Tag', $joins, $where, null, null, null, array('parent_tag_id ASC', 'tag_name ASC'), null, null, null);

        if ($tag_type =='extended_vendor') {
            $this->extendDataWithIndustryTags();
        }
    }

    function extendDataWithIndustryTags() {
        // todo should be rewriten. Added here only to allow base object validate industry tag for vendor.
        foreach(Utils::$industry_tags as $key=>$industry) {
            $this->data[$key] = $industry;
        }
    }
}