<?php

require_once('class.Link.php');

class LinkFollow extends Link {

    function LinkFollow($entity1_id, $entity1_type, $entity2_id) {
        $this->link_type = 'follow';

        parent::Link($entity1_id, $entity1_type, $entity2_id, 'company', $this->link_type);
    }

    static function getFollowsForMore($page_type, $entity_id, $follow_type, $offset) {
        $result = array();

        $entity_obj = null;
        switch ($page_type) {
            case 'user':
                $entity_obj = new User($entity_id);
                break;
            case 'vendor':
                $entity_obj = new Vendor($entity_id);
                break;
            case 'tag':
                $entity_obj = new Tag($entity_id);
                break;
            default:
                return $result;
        }

        if (!$entity_obj->is_loaded()) {
            return $result;
        }

        if ($follow_type=='following') {
            $entity_obj->load_following(true, $offset);
            $follows = $entity_obj->get_data('following');
        } else {
            $entity_obj->load_followers(true, $offset);
            $follows = $entity_obj->get_data('followers');
        }

        $html_divs = array();
        foreach ($follows as $follow) {
            Utils::$smarty->assign('follow', $follow);
            $html_divs[] = Utils::$smarty->fetch('components/front/front_follows.tpl');
        }

        $no_more = count($follows) < Utils::FOLLOWS_ON_PAGE;

        $result['html_divs'] = $html_divs;
        // todo this does not take into account case when we have last portion with count = Utils::FOLLOWS_ON_PAGE
        $result['no_more_content'] = $no_more ? 1 : 0;
        $result['offset_for_next_query'] = $offset + Utils::FOLLOWS_ON_PAGE;

        return $result;
    }
}