<?php

require_once('class.BaseObject.php');

class Tag extends BaseObject {

    protected $data;
    protected $fields;
    protected $primary_key   = 'tag_id';
    protected $secondary_key = 'code_name';
    protected $entity_name   = 'tag_name';
    protected $table_name = 'tag';
    public $logo;
    public static $logo_hash_suffix = '_t';
    const codename_url_prefix = '/blocs/';

    function Tag($tag_id = null, $code_name = null) {
        $this->fields = parent::get_fields();
        parent::BaseObject($tag_id, $code_name);
        if ($this->is_loaded()) {
            $this->data['followed_by_curr_user'] = $this->isFollowedByCurrentUser();
        }
    }

    function load($primary_id = null) {
        parent::load($primary_id);

        if ($this->data['parent_tag_id'] != 0) {
            $parent_tag                         = new Tag($this->data['parent_tag_id']);
            $this->data['parent_tag_id']        = $parent_tag->get_data('tag_id');
            $this->data['parent_tag_name']      = $parent_tag->get_data('tag_name');
            $this->data['parent_tag_code_name'] = $parent_tag->get_data('code_name');
            $this->data['parent_tag_url'] = $parent_tag->get_data('my_url');
            // todo review
        }

        $this->data['my_url'] = $this->getUrl();
        $this->data['name'] = $this->data['tag_name'];

        $no_logo = true;
        if (isset($this->data['logo_id']) && $this->data['logo_id'] > 0) {
            $this->logo = new Logo($this->data['logo_id'], null);
            if ($this->logo->is_loaded()) {
                $this->data['logo_hash'] = $this->logo->get_hash();
                $this->data['logo_url_thumb'] = $this->logo->get_data('url_thumb');
                $this->data['logo_url_full'] = $this->logo->get_data('url_full');
                $this->data['logo']['my_url'] = $this->logo->get_data("url_full");
                $this->data['logo']['my_url_thumb'] = $this->logo->get_data("url_thumb");
                $no_logo = false;
            }
        }

        if ($no_logo) {
            $this->data['logo_hash'] = '';
            $this->data['logo_url_thumb'] = '';
            $this->data['logo_url_full'] = '';
            $this->data['logo']['my_url'] = "/images/tag_tag.png";
            $this->data['logo']['my_url_thumb'] = "/images/tag_tag.png";
        }

        $url_type = 'category';
        switch ($this->data['tag_type']) {
            case 'vendor_platform':
                $url_type = 'platform';
                break;
            case 'vendor_cost':
                $url_type = 'cost';
                break;
        }
   }

    function load_name_type($tag_name, $tag_type) {
        global $db;

        $where = array('tag_name' => $tag_name, 'tag_type' => $tag_type);

        $result = $db->select($this->table_name, array('tag_id'), null, $where, array(), array(), array(), '', 1);

        if (count($result) > 0) {
            if ($result[0]['tag_id'] > 0) {
                $this->load($result[0]['tag_id'], null);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function set($data) {
        $result = parent::set($data);

        if ($result == true) {
            $this->data['code_name'] = $this->generate_code_name($this->data['tag_name'], (isset($this->data['tag_id']) ? $this->data['tag_id'] : null));
        }
        return $result;
    }

    function load_followers($limit = false, $offset = 0) {
        parent::load_followers($limit, $offset);

        // we can't load all followers and count them as it can be thousands
        $this->data['followers_count'] = Utils::getFollowCount($this->get_data('tag_id'), 'tag', true);
    }

    function getName() {
        $name = '';
        if ($this->is_loaded()) {
            $name = $this->get_data('tag_name');
        }
        return $name;
    }

    function getUrl() {
        if ($this->data['tag_type']=='contest') {
            return "/".Utils::$contest_urls[Utils::CONTEST_TOP_CONTENT_MARKETING_ID]."/" . $this->data['code_name'];
        }
        return parent::getUrl();
    }
}