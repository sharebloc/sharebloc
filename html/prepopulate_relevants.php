<?php
define('HELPER_SCRIPT', true);
//Pre populates the top 20 posts into a table so get related can then pick accordingly
require_once('../includes/global.inc.php');
require_once('../classes/class.FrontStream.php');

set_time_limit(9000);

if (!Utils::isConsoleCall() && !is_admin()) {
    redirect(Utils::INDEX_PAGE);
}
$limit = 20; //to start we only need 1 related post
$offset = 0;
$feed_parameters = array();
$feed_parameters['type'] = 'tag_top'; //pick the top 20 posts from sharebloc
$content = FrontStream::getContent($limit, $offset, $feed_parameters);
$i = 0;

$truncsql = "truncate table top_20_mv;";
$db->query($truncsql);

foreach ($content as $post){
	global $db;
	$title = $post['title'];
	
	if(strlen($title) > 72){
	// if title is too long abbreviate length
		$title = substr($title, 0, 69);
		$title .= "...";
	}


	$iframe_url = $post['iframe_url'];
	$post_id = $post['post_id'];
	
	$sql = sprintf("INSERT INTO top_20_mv
	(rank, post_id, title, iframe_url)

	VALUES
		(%d, %d, '%s', '%s')",
		$i,
		$post_id,
		$db->escape_text($title),
		$db->escape_text($iframe_url));
	$db->query($sql);
	
	$i = $i+1;
}
