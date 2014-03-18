<?php

require_once('../includes/global.inc.php');
require_once('class.ExtAuth.php');

if (!is_logged_in()) {
    redirect(Utils::getLoginUrl());
}

$join_follow_type = Utils::reqParam('type', 'bloc');

$active_join_step = 'follow';

$user = new User(get_user_id());
$user->load_follows();
$user_data = $user->get();

$skip_allowed_count = 1;

$next_follow_type = '';
$follows = array();
$only_default_follow_users = true;
$no_image_follows = false;
switch ($join_follow_type) {
    case 'bloc':
        $follows = Utils::getBlocsToFollow();
        //$follows = getCompaniesToFollow($user_data['following_by_entity_type']['tag']);
        if (User::hasOauthConnections()) {
            $next_follow_type = 'people';
        } else {
            $next_follow_type = 'networks';
        }
        $no_image_follows = true;
        break;
    case 'people':
        $follows = getUsersToFollowAndFollowThem();

        // refreshing follows
        $user->load_follows();
        $user_data = $user->get();

        $skip_allowed_count = 0;
        if (count($follows)>2) {
            $only_default_follow_users = false;
        }
        break;
    case 'networks':
        $skip_allowed_count = 0;
        $next_follow_type = 'people';
        break;
    default:
        // probably should not happen, just to be sure
        if (User::hasOauthConnections()) {
            $next_follow_type = 'people';
        } else {
            $next_follow_type = 'networks';
        }
        break;
}

$smarty_params = array(
    "follows" => $follows,
    "join_follow_type" => $join_follow_type,
    "user" => $user_data,
    'active_join_step' => $active_join_step,
    'skip_allowed_count' => $skip_allowed_count,
    'next_follow_type' => $next_follow_type,
    'only_default_follow_users' => $only_default_follow_users,
    'no_image_follows' => $no_image_follows,
);
$template->assign($smarty_params);
$template->display('pages/join_follow.tpl');

function getCompaniesToFollow($tags) {
    $follows = array();
    $vendors = Utils::getCompaniesByTags($tags, 20);
    $temp_vendors = array();
    foreach ($vendors as $vendor) {
        $temp_vendors[] = array('entity_type'=>'vendor', 'entity_id'=>$vendor['vendor_id']);
    }

    foreach ($temp_vendors as $entity) {
        $follow = Utils::prepareFollow($entity);
        $follow['follow_type'] = 'following';
        $follows[$follow['entity_uid']] = $follow;
    }

    return $follows;
}

function getDefaultFollowUsers() {
    $users = array();
    $default_user_emails = array('david@sharebloc.com', 'andrew@sharebloc.com');
    if (Settings::DEV_MODE) {
        $default_user_emails = array('bear@deepshiftlabs.com', 'bearoff@ukr.net', 'bear+david_cheng@deepshiftlabs.com');
    }
    foreach ($default_user_emails as $email) {
        $user = User::getUserByEmail($email);
        if ($user) {
            $users[] = $user['user_id'];
        }
    }

    return $users;
}

// todo simplify
function getUsersToFollowAndFollowThem() {
    $follows = array();

    $users = getDefaultFollowUsers();

    $contacts = User::getOauthContactsByUserId();
    $provider_uids = array('google'=>array(), 'linkedin'=>array(), 'twitter'=>array());
    foreach ($contacts as $contact) {
        if (!$contact['id']) {
            continue;
        }
        $provider_uids[$contact['provider']][] = "'" . Database::escapeString($contact['id']) ."'";
    }

    /* twitter */
    if ($provider_uids['twitter']) {
        $sql = sprintf("SELECT user_id FROM oauth
                        WHERE provider='twitter' AND provider_uid IN (%s)",
                        implode(', ', $provider_uids['twitter']));
        $results = Database::execArray($sql);
        foreach ($results as $result) {
            $users[] = $result['user_id'];
        }
    }

    /* linkedin */
    if ($provider_uids['twitter']) {
        $sql = sprintf("SELECT user_id FROM oauth
                        WHERE provider='linkedin' AND provider_uid IN (%s)",
                        implode(', ', $provider_uids['linkedin']));
        $results = Database::execArray($sql);
        foreach ($results as $result) {
            $users[] = $result['user_id'];
        }
    }

    /* google */
    if ($provider_uids['google']) {
        $sql = sprintf("SELECT user_id FROM user
                        WHERE email IN (%s)",
                        implode(', ', $provider_uids['google']));
        $results = Database::execArray($sql);
        foreach ($results as $result) {
            $users[] = $result['user_id'];
        }
    }

    array_unique($users);

    $temp_users = array();
    foreach ($users as $user) {
        $temp_users[] = array('entity_type'=>'user', 'entity_id'=>$user);
    }

    // todo is a bit complicated, may be we have a faster way to do this
    foreach ($temp_users as $entity) {
        // we should follow it before preparing follow array to prepare it right
        User::followUser($entity['entity_id']);
        $follow = Utils::prepareFollow($entity);
        $follow['follow_type'] = 'following';
        $follows[$follow['entity_uid']] = $follow;
    }

    return $follows;
}
