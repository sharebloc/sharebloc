<?php

require_once('../includes/global.inc.php');
require_once('class.FrontStream.php');
require_once('class.Feed.php');

$entity_codename = Utils::reqParam('code');
$entity_type = Utils::reqParam('type');

if (!$entity_codename || !$entity_type) {
    Utils::showMessagePageAndExit("This RSS feed does not exists");
}

// warn now we do not use any checks/restrictions on RSS for users and vendors and other blocs, though there are RSS buttons
// only only for 3 blocs for now

$entity = null;
$entity_type_base_name = '';
switch ($entity_type) {
    case 'blocs':
        $entity = new Tag(null, $entity_codename);
        break;
    case 'companies':
        $entity = new Vendor(null, $entity_codename);
        break;
    case 'users':
        $entity = new User(null, $entity_codename);
        break;
    default:
        Utils::showMessagePageAndExit("This RSS feed does not exists");
}

if (!$entity->is_loaded()) {
    Utils::showMessagePageAndExit("This RSS feed does not exists");
}

$entity_data = $entity->get();
$entity_id = $entity_data[$entity->get_primary_key()];
$entity_posts = FrontStream::getContent(Feed::FEED_POSTS_COUNT, 0, array('type'=>$entity->get_table_name(), 'id'=>$entity_id, 'for_rss'=>true));

Feed::outputRSS($entity_data, $entity_posts);