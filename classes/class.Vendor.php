<?php

require_once('class.BaseObject.php');
require_once('class.GenericList.php');
require_once('class.CustomList.php');
require_once('class.Tag.php');
require_once('class.TagList.php');
require_once('class.Logo.php');
require_once('class.User.php');
require_once('class.Question.php');
require_once('class.Comment.php');

class Vendor extends BaseObject {

    protected $data;
    protected $fields;
    protected $primary_key   = 'vendor_id';
    protected $secondary_key = 'code_name';
    protected $entity_name   = 'vendor_name';
    protected $table_name    = 'vendor';
    protected $required      = array('vendor_name');
    private $tag_list;
    private $user_list;
    private $company_list;
    private $similar_list;
    private $also_viewed_list;
    public $logo;
    public static $logo_hash_suffix = '_v';
    const codename_url_prefix = '/companies/';

    function Vendor($vendor_id = null, $code_name = null) {
        $this->tag_list      = new TagList('extended_vendor');

        parent::BaseObject($vendor_id, $code_name);

        $this->fields['tag_list']      = array('type'    => 'list', 'options' => $this->tag_list->get_options_list('tag_id', array('tag_id', 'tag_name', 'parent_tag_id')));
        if ($this->is_loaded()) {
            $this->data['followed_by_curr_user'] = $this->isFollowedByCurrentUser();
        } else {
            $this->data['logo']['my_url_thumb'] = "/images/company.png";
        }
    }

    function load($primary_id = null, $secondary_id = null) {
        parent::load($primary_id, $secondary_id);

        if (isset($this->data['vendor_id'])) {
            $this->tag_list->set_selection_criteria('tag_selection', array('entity_id'   => $this->data['vendor_id'], 'entity_type' => 'vendor', 'tag_type'    => 'vendor'), 'tag_id');
            $this->tag_list->load_selections();
            $this->data['tag_list']         = $this->tag_list->get_selections_list();
            $this->data['tag_list_details'] = $this->tag_list->get_selections_list_details();
            $this->setExtendedTags();

            $no_logo = true;
            if (isset($this->data['logo_id']) && $this->data['logo_id'] > 0) {
                $this->logo              = new Logo($this->data['logo_id']);
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
                $this->data['logo']['my_url'] = "/images/company.png";
                $this->data['logo']['my_url_thumb'] = "/images/company.png";
            }

            $this->data['my_url'] = $this->getUrl();
            $this->data['name'] = $this->data['vendor_name'];

            $this->load_user_count();
        }
    }

    function load_by_name($vendor_name) {
        global $db;

        $where = array('vendor_name' => $vendor_name);

        $result = $db->select($this->table_name, array('vendor_id'), null, $where, array(), array(), array(), '', 1);

        if (count($result) > 0) {
            if ($result[0]['vendor_id'] > 0) {
                $this->load($result[0]['vendor_id'], null);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function set($data) {
        if (empty($data['logo_id']) && !empty($data['logo_hash'])) {
            $logo            = new Logo(null, $data['logo_hash']);
            $data['logo_id'] = $logo->get_data('logo_id');
            unset($logo);
        }

        $result = parent::set($data);

        if ($result == true) {
            $this->data['code_name'] = $this->generate_code_name($this->data['vendor_name'], ( isset($this->data['vendor_id']) ? $this->data['vendor_id'] : null));
        }

        if (!is_object($this->tag_list) && isset($this->data['vendor_id']) && $this->data['vendor_id'] > 0) {
            $this->tag_list = new TagList('vendor');
            $this->tag_list->set_selection_criteria('tag_selection', array('entity_id'   => $this->data['vendor_id'], 'entity_type' => 'vendor', 'tag_type'    => 'vendor'), 'tag_id');
        }

        if (!isset($data['tag_list']) || !is_array($data['tag_list'])) {
            $data['tag_list'] = array();
        }

        $this->tag_list->set_selections($data['tag_list']);
        if (!empty($data['logo_hash'])) {
            $logo = new Logo(null, $data['logo_hash']);
            if ($logo->get_data('logo_hash') !== $this->data['code_name'] . $this::$logo_hash_suffix) {
                $logo->rename($this->data['code_name'] . $this::$logo_hash_suffix);
            }

            $this->data['logo_id'] = $logo->get_data('logo_id');
        }

        return $result;
    }

    function save_data() {
        $primary_id = parent::save_data();

        if ($primary_id > 0) {
            $this->tag_list->set_selection_criteria('tag_selection', array('entity_id'   => $primary_id, 'entity_type' => 'vendor', 'tag_type'    => 'vendor'), 'tag_id');
            $this->tag_list->save_selections();
        }

        return $primary_id;
    }

    function delete() {
        global $db;

        if (isset($this->data['logo_id']) && $this->data['logo_id'] > 0) {
            $this->logo = new Logo($this->data['logo_id']);
            $this->logo->delete();
        }

        $dquery = "DELETE FROM vendor WHERE vendor_id = '" . $db->escape_text($this->data['vendor_id']) . "'";
        $db->query($dquery);

        $dquery = "DELETE FROM link WHERE ( entity1_type = 'vendor' AND entity1_id = '" . $db->escape_text($this->data['vendor_id']) . "' ) OR ( entity2_type = 'vendor' AND entity2_id = '" . $db->escape_text($this->data['vendor_id']) . "' )";
        $db->query($dquery);

        $dquery = "DELETE FROM rating WHERE vendor_id = '" . $db->escape_text($this->data['vendor_id']) . "'";
        $db->query($dquery);

        $dquery = "DELETE FROM screenshot WHERE entity_type = 'vendor' AND entity_id = '" . $db->escape_text($this->data['vendor_id']) . "'";
        $db->query($dquery);

        $dquery = "DELETE FROM tag_selection WHERE entity_type = 'vendor' AND entity_id = '" . $db->escape_text($this->data['vendor_id']) . "'";
        $db->query($dquery);

        $dquery = "UPDATE posted_link SET author_vendor_id=0 WHERE author_vendor_id = " . intval($this->data['vendor_id']);
        $db->query($dquery);

        $this->recache();
    }

    function set_users($users) {
        global $db;

        // todo bear here should be transaction start
        $where = array(
            'entity2_id'   => $this->data['vendor_id'],
            'entity2_type' => 'vendor',
            'link_type'    => 'use'
        );
        $db->delete('link', $where, null);

        foreach ($users['company_ids'] AS $company_id) {
            $link_use = new LinkUse($company_id, 'company', $this->data['vendor_id']);
            $link_use->add();
        }
        foreach ($users['user_ids'] AS $user_id) {
            $link_use = new LinkUse($user_id, 'user', $this->data['vendor_id']);
            $link_use->add();
        }

        $this->recache();
    }

    function add_user($user_id, $type) {
        if (!$this->is_loaded()) {
            return false;
        }
        $link_use = new LinkUse($user_id, $type, $this->data['vendor_id']);
        $link_use->add();
        $this->recache();
    }

    function load_user_count() {
        global $db;

        $squery  = "SELECT count(DISTINCT CASE WHEN u.company_id > 0 THEN u.company_id ELSE li.entity1_id END ) AS count " .
                "FROM link li LEFT JOIN user u ON u.user_id = li.entity1_id AND li.entity1_type = 'user' " .
                "WHERE li.entity2_id = '" . $db->escape_text($this->data['vendor_id']) . "' and li.entity2_type = 'vendor'";
        $sresult = $db->query($squery);

        $this->data['user_count'] = 0;

        if (is_array($sresult) && count($sresult) > 0) {
            $this->data['user_count'] = $sresult[0]['count'];
        }
    }

    function load_similar_list() {
        $this->similar_list = null;

        $this->data['similar_list']     = array();
        $this->data['similar_count']    = 0;

        if (count($this->data['tag_list']) > 0) {
            $joins              = array();
            $joins[]            = array('type'   => 'inner', 'table'  => 'tag_selection', 'fields' => null, 'on'     => array('entity_id'   => 'vendor_id', 'entity_type' => '"vendor"'), 'where'  => array('tag_id' => $this->data['tag_list']));
            $this->similar_list = new GenericList('vendor', $joins, null, array('vendor_id' => $this->data['vendor_id']), null, null, array('rand()'), 3, 0, null);

            $this->data['similar_list'] = $this->similar_list->get();

            if (is_array($this->data['similar_list'])) {
                foreach ($this->data['similar_list'] AS $key => $more) {
                    $first_tag_list_details         = reset($more['tag_list_details']);
                    $this->data['similar_category'] = $first_tag_list_details['code_name'];
                    break;
                }
            }
        }
    }

    // todo review
    static function getMainTag($vendor_id) {
        $main_tag = null;
        if (!$vendor_id) {
            return $main_tag;
        }

        $vendor = new Vendor($vendor_id);

        $tags = $vendor->get_data('tag_list_details');

        if (!$tags) {
            return $main_tag;
        }

        foreach ($tags as $tag_data) {
            if ($tag_data['tag_type']=='industry') {
                continue;
            }

            if (!$main_tag) {
                $main_tag = $tag_data;
            }
            if (!$tag_data['parent_tag_id']) {
                $main_tag = $tag_data;
                break;
            }
        }

        return $main_tag;
    }

    function setExtendedTags() {
        $tags = array();
        $this->data['industry_tag'] = array();
        foreach($this->data['tag_list_details'] as $tag) {
            $parent_id = $tag['parent_tag_id'];
            if (!$parent_id) {
                continue;
            }

            if ($tag['tag_type']=='industry') {
                $this->data['industry_tag'] = $tag;
                continue;
            }

            $tags[$tag['tag_id']] = $tag;

            if (!isset($tags[$parent_id])) {
                $temp_tag = new Tag($parent_id);
                $tags[$parent_id] = $temp_tag->get();
            }
        }
        $this->data['extended_tags'] = $tags;
    }

    /*
     * When the "also viewed" table is empty for this vendor, the "similar" list is used.
     */

    function load_also_viewed_list() {
        $this->also_viewed_list = null;

        $this->data['also_viewed_list'] = array();
        $also_viewed                    = new AlsoViewed('vendor', $this->data['vendor_id']);
        $also_viewed_ids                = $also_viewed->get_also_viewed_ids();

        if (count($also_viewed_ids)) {
            $this->also_viewed_list         = new GenericList('vendor', array(), array('vendor_id' => $also_viewed_ids), array());
            $this->data['also_viewed_list'] = $this->also_viewed_list->get();
        } else {
            if (is_null($this->similar_list)) {
                // load_similar_list() was not already called
                $this->load_similar_list();
            }
            // warn - also_viewed_list is only a reference to the similar_list object
            $this->also_viewed_list = $this->similar_list;
            if ($this->also_viewed_list) {
                $this->data['also_viewed_list'] = $this->also_viewed_list->get();
            }
        }
    }

    function get_seo_attributes() {
        $used_categories = array();

        $seo_attributes                = array();
        $seo_attributes['title']       = '';
        $seo_attributes['keywords']    = '';
        $seo_attributes['description'] = '';

        if ($this->is_loaded()) {
            $seo_attributes['title']    = $this->data['vendor_name'];
            $seo_attributes['keywords'] = $this->data['vendor_name'];

            $tag_names = array();
            foreach ($this->data['tag_list_details'] AS $tag_detail) {
                if (!in_array($tag_detail['tag_name'], $used_categories)) {
                    $seo_attributes['keywords'] .= ", " . $tag_detail['tag_name'];
                    $used_categories[] = $tag_detail['tag_name'];
                }
            }

            foreach ($this->data['tag_list_details'] AS $tag_detail) {
                if (empty($tag_detail['parent_tag_name'])) {
                    continue;
                }
                if (!in_array($tag_detail['parent_tag_name'], $used_categories)) {
                    $seo_attributes['keywords'] .= ", " . $tag_detail['parent_tag_name'];
                    $used_categories[] = $tag_detail['parent_tag_name'];
                }
            }

            if (strlen($this->data['description']) > 3) {
                if (strlen($this->data['description']) < 100) {
                    $seo_attributes['description'] = $this->data['description'];
                } else {
                    $pos                           = strpos($this->data['description'], ' ', 100);
                    $seo_attributes['description'] = substr($this->data['description'], 0, $pos) . "...";
                }
            } else {
                $seo_attributes['description'] = $this->data['vendor_name'];
            }
        }

        return $seo_attributes;
    }

    function getName() {
        $name = '';
        if ($this->is_loaded()) {
            $name = $this->get_data('vendor_name');
        }
        return $name;
    }

    function getEmailDomain() {
        $email_domain = '';
        if ($this->is_loaded()) {
            $url          = $this->get_data('website');
            $email_domain = getEmailDomainFromUrl($url);
        }
        return $email_domain;
    }

    function checkEmailDomain($email) {
        if (!$this->is_loaded()) {
            return false;
        }
        $my_email_domain = $this->getEmailDomain();
        $email_parts     = explode('@', $email);
        if ($my_email_domain && strtolower($email_parts[1]) !== $my_email_domain) {
            return false;
        }

        return true;
    }

    function setClaimBlockFlag($value = true) {
        $f_claim_locked = 0;
        if ($value) {
            $f_claim_locked = 1;
        }

        if (!$this->is_loaded()) {
            return false;
        }

        $my_current_data                   = $this->get();
        $my_current_data["f_claim_locked"] = $f_claim_locked;
        $this->set($my_current_data);
        $this->save();
    }

    function approveClaim($user_id) {
        if (!$this->is_loaded()) {
            return;
        }

        $my_current_data                  = $this->get();
        $my_current_data["owner_user_id"] = $user_id;
        $this->set($my_current_data);
        $this->save();

        if ($my_current_data["f_claim_locked"]) {
            $this->setClaimBlockFlag(false);
        }

        return true;
    }

    function deleteClaim() {
        if (!$this->is_loaded()) {
            return;
        }

        $my_current_data                  = $this->get();
        $my_current_data["owner_user_id"] = 0; // todo bear this should be changed to NULL
        $this->set($my_current_data);
        $this->save();
        return true;
    }

    // this is not a best place for this function but placing it inline in the vendor.php as other spaghetti code there is worse
    static function processClaimInvite($vendor) {
        /*TODO remove - Not used anymore */
        $should_clean = get_input('delete_claimant_hidden');
        $should_add   = get_input('new_claimant_hidden');
        if (!$should_clean && !$should_add) {
            // nothing to do
            return true;
        }

        if ($should_clean) {
            $current_data                  = $vendor->get();
            $current_data['owner_user_id'] = 0;
            $vendor->set($current_data);
            $vendor->save();
            Claim::deletePreviousEntityClaims($vendor->get_data("vendor_id"), 'vendor');
        }
        if ($should_add) {
            $first_name = trim(get_input('claimant_first_name_hidden'));
            $last_name  = trim(get_input('claimant_last_name_hidden'));
            $email      = trim(get_input('claimant_email_hidden'));
            if ($vendor->get_data('owner_user_id')) {
                Log::$logger->error("We have new claimant by admin but vendor has not deleted owner already.");
                return "Please delete previous claimant to add another one.";
            }

            if (!$first_name || !$last_name || !$email) {
                return "Please fill all the fields in.";
            }

            if (!validate_email($email)) {
                return "You must specify a valid e-mail address.";
            }

            $temp_user = new User();
            $temp_user->load_email($email);

            if (!$temp_user->is_loaded()) {
                return "Can't find a user with email $email.";
            }

            $result = $temp_user->claimVendor($first_name, $last_name, $email, $vendor->get_data("vendor_id"), true);
            if ($result !== true) {
                return $result;
            }
        }
        return true;
    }

    /* MOVED FROM COMPANY */

    function get_vendor_list($tag_ids = null, $ignore_users = false) {
        global $db;

        if ($this->is_loaded()) {

            $join_add = "";

            $where = array();

            if (!empty($tag_ids) && is_array($tag_ids)) {
                $join_add               = " INNER JOIN tag_selection ts ON ts.entity_id = v.vendor_id AND ts.entity_type = 'vendor' ";
                $where['ts.tag_id']   = $tag_ids;
                $where['ts.tag_type'] = "'vendor'";
            }

            $where_add = "";

            if (count($where) > 0) {
                $where_add = " AND " . $db->prepare_where_text($where, 'v');
            }

            $user_where = "";

            if ($ignore_users == false) {
                $user_where = "   OR ( li.entity1_type = 'user' AND u.company_id = '" . $db->escape_text($this->data['company_id']) . "' ) " .
                        "        AND u.privacy = 'public' ";
            }

            $custom_query = "SELECT DISTINCT v.vendor_id " .
                    "FROM vendor v " .
                    "LEFT JOIN link li ON li.entity2_id = v.vendor_id AND li.entity2_type = 'vendor' AND li.entity1_type IN ( 'company', 'user' )  " .
                    "LEFT JOIN user u ON u.user_id = li.entity1_id AND li.entity1_type = 'user' " .
                    $join_add .
                    "WHERE " .
                    " ( " .
                    "      ( li.entity1_type = 'company' AND li.entity1_id = '" . $db->escape_text($this->data['company_id']) . "' ) " .
                    $user_where .
                    " ) " .
                    $where_add;

            $vendor_list = new CustomList('vendor', $custom_query);
            $vendor_list->set_containing_entity('company', $this->get_data('company_id'));

            return $vendor_list;
        }
    }

    function load_vendor_list() {
        global $db;

        if ($this->is_loaded()) {
            $this->data['vendor_list']  = array();
            $this->data['vendor_count'] = 0;

            $vendor_list = $this->get_vendor_list();

            $this->data['vendor_list']  = $vendor_list->get();
            $this->data['vendor_count'] = count($this->data['vendor_list']);
        }
    }

    static function getEmptyProfileData() {
        $data = array();
        $data['vendor_id'] = array('type'=>'hidden', 'title'=>'Vendor Id', 'value'=>'', 'f_needed' => false);
        $data['vendor_name'] = array('type'=>'input', 'title'=>'Company Name', 'value'=>'', 'f_needed' => true);
        $data['location'] = array('type'=>'input', 'title'=>'Location', 'value'=>'', 'f_needed' => false);
        $data['about'] = array('type'=>'input', 'title'=>'Byline', 'value'=>'', 'f_needed' => false, 'max_length'=>Utils::MAX_ABOUT_LENGTH);
        $data['industries'] = array('type'=>'select', 'title'=>'Industry', 'value'=>'', 'f_needed' => true);
        $data['company_size'] = array('type'=>'select', 'title'=>'Company Size', 'value'=>'', 'f_needed' => false);
        $data['blocs'] = array('type'=>'select', 'title'=>'Blocs', 'value'=>'', 'f_needed' => false);
        $data['description'] = array('type'=>'input', 'title'=>'Summary', 'value'=>'', 'f_needed' => false);
        $data['website'] = array('type'=>'input', 'title'=>'Website', 'value'=>'', 'f_needed' => false);
        $data['linkedin'] = array('type'=>'input', 'title'=>'LinkedIn', 'value'=>'', 'f_needed' => false);
        $data['facebook'] = array('type'=>'input', 'title'=>'Facebook', 'value'=>'', 'f_needed' => false);
        $data['twitter'] = array('type'=>'input', 'title'=>'Twitter', 'value'=>'', 'f_needed' => false);
        $data['google_plus'] = array('type'=>'input', 'title'=>'Google Plus', 'value'=>'', 'f_needed' => false);
        $data['autopost_tag_id'] = array('type'=>'select', 'title'=>'Auto-post bloc', 'value'=>'', 'f_needed' => false);
        $data['f_autopost'] = array('type'=>'checkbox', 'title'=>'Post from RSS', 'value'=>'', 'f_needed' => false);
        $data['rss'] = array('type'=>'input', 'title'=>'RSS', 'value'=>'', 'f_needed' => false);

        return $data;
    }

    static function getProfileInfoFromRequest() {
        $data = self::getEmptyProfileData();

        foreach ($data as $key=>&$field) {
            if ($field['type']==='checkbox') {
                $field['value'] = Utils::reqParam($key) ? 1 : 0;
            } else {
                $field['value'] = Utils::reqParam($key);
            }
        }

        return $data;
    }

    static function validateProfileData($data) {
        $errors = Utils::validateCommonProfileData($data);

        $autopost_tag_id = $data['autopost_tag_id']['value'];
        $f_autopost = $data['f_autopost']['value'];
        if ($f_autopost &&
                (!$autopost_tag_id || !isset(Utils::$tags_parents_vendor[$autopost_tag_id]))
            )
        {
            $errors['autopost_tag_id'] = array('name'=>'autopost_tag_id', 'msg'=>"You must select default bloc for auto-post.");
        }

        require_once('class.Feed.php');
        if ($f_autopost && !Feed::validateRSSUrl($data['rss']['value'])) {
            $errors['rss'] = array('name'=>'rss', 'msg'=>"You must enter valid RSS url.");
        }
        return $errors;
    }

    static function saveVendorProfile($data) {
        if (!$data['vendor_id']['value']) {
            // new vendor will be created
            $data['vendor_id']['value'] = null;
        }

        $vendor = new Vendor($data['vendor_id']['value']);

        $vendor_data = $vendor->get();
        $vendor_data['vendor_name'] = $data['vendor_name']['value'];
        $vendor_data['location'] = $data['location']['value'];
        $vendor_data['about'] = $data['about']['value'];

        $vendor_data['company_size'] = $data['company_size']['value'];
        $vendor_data['description'] = $data['description']['value'];
        $vendor_data['website'] = $data['website']['value'];
        $vendor_data['linkedin'] = $data['linkedin']['value'];
        $vendor_data['facebook'] = $data['facebook']['value'];
        $vendor_data['twitter'] = $data['twitter']['value'];
        $vendor_data['google_plus'] = $data['google_plus']['value'];

        $vendor_data['f_autopost'] = $data['f_autopost']['value'];
        $vendor_data['rss'] = $data['rss']['value'];
        $vendor_data['autopost_tag_id'] = 0;
        if ($data['f_autopost']['value']) {
            $vendor_data['autopost_tag_id'] = $data['autopost_tag_id']['value'];
        }

        $industries = $data['industries']['value'];
        $blocs = $data['blocs']['value'];
        if (!$blocs) {
            $blocs = array();
        }
        $vendor_data['tag_list'] = array_merge($blocs, $industries);

        if (!$vendor->set($vendor_data)) {
            $redirect_url = Utils::getBaseUrl() . $vendor->data['my_url'];
            $_SESSION['alert_message'] = Gate::MSG_ERR_UNKNOWN;
            return $redirect_url . "/edit?t=" . time();
        }

        $vendor->save();

        if (Utils::sVar('not_finished_upload_logo')) {
            $vendor->attachUploadedLogoToNewEntity();
        }

        $redirect_url = Utils::getBaseUrl() . $vendor->data['my_url'];

        return $redirect_url;
    }

    /**
     * @param integer $merge_company_id - destination company id
     * Moves company users (company team) to destination company (users' vendors and reviews will belong to destination company too).
     * Copies source company vendors, tags and followers to destination company.
     * WARN! Source company users' (company team) vendors are copied to destination company as vendors selected by admin.
     * @author bear@deepshiftlabs.com - 26 March 2013
     */
    function merge($merge_company_id) {
        global $db;

        $dest_company = new Vendor($merge_company_id);
        if (!$dest_company->is_loaded()) {
            return false;
        }

        /* loading additional data needed */
        $this->load_team_list();

        $this->load_vendor_list();
        $dest_company->load_vendor_list();

        $this->load_follower_list();
        $dest_company->load_follower_list();

        /* data validation and merge */
        /* tags */
        $tag_list_source = $this->get_data("tag_list");
        if (!is_array($tag_list_source)) {
            $tag_list_source = array();
        }
        $tag_list_dest = $dest_company->get_data("tag_list");
        if (!is_array($tag_list_dest)) {
            $tag_list_dest = array();
        }
        $result_tags = array_merge($tag_list_source, $tag_list_dest);

        /* followers */
        $follower_list_source = $this->get_data("follower_list");
        if (is_array($follower_list_source)) {
            $follower_list_source = array_keys($follower_list_source);
        } else {
            $follower_list_source = array();
        }

        $follower_list_dest = $dest_company->get_data("follower_list");
        if (is_array($follower_list_dest)) {
            $follower_list_dest = array_keys($follower_list_dest);
        } else {
            $follower_list_dest = array();
        }

        /* vendors */
        $vendor_list_source = $this->get_data("vendor_list");
        if (is_array($vendor_list_source)) {
            $vendor_list_source = array_keys($vendor_list_source);
        } else {
            $vendor_list_source = array();
        }

        $vendor_list_dest = $dest_company->get_data("vendor_list");
        if (is_array($vendor_list_dest)) {
            $vendor_list_dest = array_keys($vendor_list_dest);
        } else {
            $vendor_list_dest = array();
        }

        $result_vendors = array_merge($vendor_list_source, $vendor_list_dest);

        /* users */
        $users_to_move = $this->get_data("team_list");
        if (is_array($users_to_move)) {
            $users_to_move = array_keys($users_to_move);
        } else {
            $users_to_move = array();
        }

        /* Data copying and moving */

        /* Adding tags */
        $dest_company->set(array('tag_list' => $result_tags));
        $dest_company->save();

        /* Adding followers */
        foreach ($follower_list_source as $follower_id) {
            if (in_array($follower_id, $follower_list_dest)) {
                // this user already follow destination company
                continue;
            }
            $link_follow = new LinkFollow($follower_id, 'user', $dest_company->data['company_id']);
            $link_follow->add();
        }

        /* Adding vendors */
        // the approach copied from cmd.php 'set_vendors' command
        $where = array(
        'entity1_id'   => $dest_company->data['company_id'],
        'entity1_type' => "company",
        'entity2_type' => 'vendor',
        'link_type'    => 'use'
        );
        $db->delete('link', $where, null);

        foreach ($result_vendors AS $vendor_id) {
            $link_use   = new LinkUse($dest_company->data['company_id'], 'company', $vendor_id);
            $link_use->add();
            $vendor_obj = new Vendor($vendor_id);
            $vendor_obj->recache();
        }

        /* Resign company team */
        $data = array("company_id" => $dest_company->data['company_id']);
        foreach ($users_to_move as $user_id) {
            $user = new User($user_id);
            $user->set($data);
            $user->save();
        }

        /* Recashing */
        $dest_company->recache();
        $this->recache();
        return true;
    }

    function getProperSizeValue($size_text) {
        // we have other wording than linkedin
        $size_text = trim(str_replace(',', '', $size_text));
        if ($size_text === "10001+") {
            $size_text = "10000+";
        }

        $sizes = $this->fields['company_size']['options'];
        if (in_array($size_text, $sizes)) {
            return $size_text;
        } else {
            return '';
        }
    }

    function getClaimantData() {
        $claimant_data = false;

        if ($this->get_data('owner_user_id')) {
            $owner_user                    = new User($this->get_data('owner_user_id'));
            $claimant_data                 = $owner_user->get();
            $claimant_data['claim_status'] = 'confirmed';
        } else {
            $unconfirmed_claim = Claim::getActiveClaimByEntity($this->get_data('vendor_id'), "vendor");
            if ($unconfirmed_claim) {
                $claim_user                      = new User($unconfirmed_claim['user_id']);
                $claim_user_data                 = $claim_user->get();
                $claim_user_data['claim_status'] = 'unconfirmed <br>(was sent ' . $unconfirmed_claim['created_ts'] . ')';
            }
        }
        return $claimant_data;
    }

    // todo deprecated, to remove
    static function checkAndProcessVendorClaim($code_name) {
        if (strpos($code_name, 'claimkey') !== 0) {
            return;
        }

        $claim_key = str_replace('claimkey', '', $code_name);
        $code_name = '';

        if (!is_logged_in()) {
            Utils::$smarty->assign('message', "You should be <a href='".Utils::getLoginUrl()."'>logged in</a> to confirm your claim.");
            Utils::$smarty->display('pages/message.tpl');
            exit();
        }

        $vendor_id = Claim::processClaimKey($claim_key);
        if ($vendor_id === false) {
            Utils::$smarty->assign('message', "Your Claim Confirmation Key is Invalid.");
            Utils::$smarty->display('pages/message.tpl');
            exit();
        }

        // redirecting to the edit page.
        // If we need in redirecting to view page, this can be commented out as we will be redirected ti view page below.
        $vendor     = new Vendor($vendor_id);
        $entity_url = $vendor->getUrl();
        redirect($entity_url . "/edit");
    }
}