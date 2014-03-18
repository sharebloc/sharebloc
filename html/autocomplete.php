<?php

require_once('../includes/global.inc.php');
require_once('class.Vendor.php');
require_once('class.GenericList.php');

$callback      = get_input('callback');
$feature_class = get_input('featureClass');
$starts_with   = get_input('name_startsWith');
$max_rows      = get_input('maxRows');

if ($feature_class == "search[name]") {
    $vendor_list = new GenericList('vendor', null, array('vendor_name' => $starts_with . '%'), null, null, null, array('vendor_name ASC'), 3, 0, null);
    $results     = $vendor_list->get();

    $output = array('totalResults' => 0,
        'results'      => array()
    );

    if (count($results) > 0) {
        $output['totalResults'] += count($results);

        foreach ($results AS $result) {
            $output['results'][] = array('ID'   => $result['vendor_id'], 'Name' => $result['vendor_name'] . " (Vendor)", 'Code' => $result['code_name'], 'Type' => 'vendor');
        }
    }

    $company_list = new GenericList('vendor', null, array('vendor_name' => $starts_with . '%'), null, null, null, array('vendor_name ASC'), 3, 0, null);
    $results      = $company_list->get();

    if (count($results) > 0) {
        $output['totalResults'] += count($results);

        foreach ($results AS $result) {
            $output['results'][] = array('ID'   => $result['vendor_id'], 'Name' => $result['vendor_name'] . " (Company)", 'Code' => $result['code_name'], 'Type' => 'company');
        }
    }

    $category_list = new GenericList('tag', null, array('tag_name' => $starts_with . '%', 'tag_type' => array('vendor' /* ,'company' */)), null, null, null, array('tag_name ASC'), 3, 0, null);
    $results       = $category_list->get();

    if (count($results) > 0) {
        $output['totalResults'] += count($results);

        foreach ($results AS $result) {
            $output['results'][] = array('ID'   => $result['tag_id'], 'Name' => $result['tag_name'] . " (Category)", 'Code' => $result['tag_id'], 'Type' => 'category');
        }
    }
} else if ($feature_class == "user[company_name]") {
    $company_list = new GenericList('vendor', null, array('vendor_name' => $starts_with . '%'), null, null, null, array('vendor_name ASC'), 3, 0, null);
    $results      = $company_list->get();

    $output = array('totalResults' => count($results),
        'results'      => array()
    );

    if (count($results) > 0) {
        foreach ($results AS $result) {
            $output['results'][] = array('ID'   => $result['vendor_id'], 'Name' => $result['vendor_name'], 'Code' => $result['code_name']);
        }
    }
}  else if ($feature_class == "search[vendor]") {
    $output = array('totalResults' => 0,
                'results' => array()
            );
    // todo bear we can't used this directly in queries now as we have no support for "in" in "not_where" for GenericList
    $ignore_ids = get_input('ignore_ids');

    $vendor_list = new GenericList('vendor', null, array('vendor_name' => $starts_with . '%'), null, null, null, array('vendor_name ASC'), 3, 0, null);
    $results     = $vendor_list->get();

    if ($results) {
        foreach ($results AS $result) {
            if (is_array($ignore_ids) && in_array($result['vendor_id'], $ignore_ids)) {
                continue;
            }

            $logo_hash    = empty($result['logo_hash']) ? '' : $result['logo_hash'];
            $vendor_name = $result['vendor_name'];

            $output['results'][] = array('ID' => $result['vendor_id'],
                                        'Name' => $vendor_name,
                                        'Code' => $result['code_name'],
                                        'Type' => 'vendor',
                                        'Logo_hash'=> $logo_hash);
            $output['totalResults']++;
        }
    }

} else if ($feature_class == "search[customer]") {
    $output         = array('totalResults' => 0,
        'results'      => array()
    );
    $only_companies = get_input('only_companies');

    // todo bear we can't used this directly in queries now as we have no support for "in" in "not_where" for GenericList
    $ignore_ids = get_input('ignore_ids');

    $company_list = new GenericList('vendor', null, array('vendor_name' => $starts_with . '%'), null, null, null, array('vendor_name ASC'), 5, 0, null);
    $results      = $company_list->get();

    if ($results) {
        foreach ($results AS $result) {
            if (!empty($ignore_ids['company_ids']) && in_array($result['vendor_id'], $ignore_ids['company_ids'])) {
                continue;
            }
            $logo_hash    = empty($result['logo_hash']) ? '' : $result['logo_hash'];
            $company_name = $result['vendor_name'];
            $email_domain = getEmailDomainFromUrl($result['website']);
            if (!$only_companies) {
                $company_name .= " (Company)";
            }

            $output['results'][] = array('ID'          => $result['vendor_id'], 'Name'        => $company_name, 'Code'        => $result['code_name'], 'Type'        => 'company', 'Logo_hash'   => $logo_hash, 'Id'          => $result['vendor_id'], 'EmailDomain' => $email_domain);
            $output['totalResults']++;
        }
    }

    if (!$only_companies) {
        $user_list = new GenericList('user', null, array('first_name' => $starts_with . '%', 'last_name'  => $starts_with . '%'), null, null, null, array('first_name ASC', 'last_name ASC'), 5, 0, null);
        $results   = $user_list->get();

        if ($results) {
            foreach ($results AS $result) {
                if (!empty($ignore_ids['user_ids']) && in_array($result['user_id'], $ignore_ids['user_ids'])) {
                    continue;
                }
                $logo_hash           = empty($result['logo_hash']) ? '' : $result['logo_hash'];
                $output['results'][] = array('ID'          => $result['user_id'], 'Name'        => $result['first_name'] . " " . $result['last_name'] . " (User)", 'Code'        => $result['code_name'], 'Type'        => 'user', 'Logo_hash'   => $logo_hash, 'Id'          => $result['user_id'], 'EmailDomain' => '');
                $output['totalResults']++;
            }
        }
    }
}

echo $callback . "(";
echo json_encode($output);
echo ")";
?>