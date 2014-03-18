<?php

if (file_exists('/var/www/htdocs/vendorstack/includes/global.inc.php'))
    require_once('/var/www/htdocs/vendorstack/includes/global.inc.php');
if (file_exists('/var/www/htdocs/vendorstack_beta/includes/global.inc.php'))
    require_once('/var/www/htdocs/vendorstack_beta/includes/global.inc.php');
else if (file_exists('/Users/dheliker/Sites/vendorstack/includes/global.inc.php'))
    require_once('/Users/dheliker/Sites/vendorstack/includes/global.inc.php');
else
    exit();

$num_to_recache = 10;

$db_recache = 1;

$squery = "SELECT vendor_id, code_name FROM vendor ORDER BY rand() LIMIT $num_to_recache";

$sresult = $db->query($squery);

foreach ($sresult AS $vendor) {
    $v = new Vendor($vendor['vendor_id']);

    echo "Recached Vendor: " . $v->get_data('vendor_id') . " - " . $v->get_data('code_name') . "\n";
}

$squery = "SELECT company_id, code_name FROM company ORDER BY rand() LIMIT $num_to_recache";

$sresult = $db->query($squery);

foreach ($sresult AS $company) {
    $c = new Company($company['company_id']);

    echo "Recached Company: " . $c->get_data('company_id') . " - " . $c->get_data('code_name') . "\n";
}

$squery = "SELECT question_id, code_name FROM question ORDER BY rand() LIMIT $num_to_recache";

$sresult = $db->query($squery);

foreach ($sresult AS $question) {
    $q = new Question($question['question_id']);

    $q->load_answers();

    echo "Recached Question: " . $q->get_data('question_id') . " - " . $q->get_data('code_name') . "\n";
}
?>