<?php

require_once('../includes/global.inc.php');
require_once('class.FrontStream.php');

$SECONDS_FOR_ERROR = 10;

$start_ts = microtime(true);
Log::$logger->warn("Started sitemap generation");

$urls    = getSitemapUrls();
$sitemap = getSitemapXML($urls);

header("Content-type: text/xml");
header('Content-Disposition: attachment; filename="sitemap.xml"');
echo($sitemap);

Log::$logger->warn("Sitemap generation finished.");

$diff = microtime(true) - $start_ts;
if ($diff > $SECONDS_FOR_ERROR) {
    $msg = "Too long sitemap generation, time = " . $diff;
    Log::$logger->error($msg);
}

exit();

function getSitemapUrls() {
    $urls = array();

    $sql = "SELECT code_name, 'vendor' AS entity_type FROM vendor
            UNION
            SELECT code_name, 'posted_link' AS entity_type FROM posted_link
            UNION
            SELECT code_name, 'user' AS entity_type FROM user
            UNION
            SELECT code_name, 'question' AS entity_type FROM question
            UNION
            SELECT code_name, 'tag' AS entity_type FROM tag
                WHERE parent_tag_id=0";
    $entities = Database::execArray($sql);

    $base_url = Utils::getBaseUrl();

    foreach ($entities as $entity) {
        if (!$entity['code_name']) {
            continue;
        }
        switch($entity['entity_type']) {
            case 'vendor':
                $url = Vendor::getUrlByCodename($entity['code_name']);
                break;
            case 'posted_link':
                $url = PostedLink::getUrlByCodename($entity['code_name']);
                break;
            case 'user':
                $url = User::getUrlByCodename($entity['code_name']);
                break;
            case 'question':
                $url = Question::getUrlByCodename($entity['code_name']);
                break;
            case 'tag':
                $url = Tag::getUrlByCodename($entity['code_name']);
                break;
        }

        $urls[] = $base_url . $url;
    }
    return $urls;
}

function getSitemapXML($urls) {
    $sitemap = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $sitemap .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

    foreach ($urls as $url) {
        $sitemap .= sprintf(
                "    <url>
        <loc>%s</loc>
    </url>\n"
                , $url);
    }

    $sitemap .= '</urlset>';

    return $sitemap;
}
