<?php

/**
 * This is a container for functions related to vendors and companies  DE->VS import.
 * @copyright (C) 2013 ShareBloc
 * @author bear@deepshiftlabs.com
 * @since 22 may 2013
 */
// todo bear - should replace error_log with normal logging
class DEImport {

    static private function load_DE_entities($de_entities_ids) {
        global $db;

        array_walk($de_entities_ids, function(&$v_id) {
                    $v_id = intval($v_id);
                });

        $sql = sprintf("SELECT * FROM data_vendors
                WHERE id IN (%s)
                AND status IN ('ready', 'verified')", implode(',', $de_entities_ids));

        $entities = $db->query($sql);
        if (!$entities) {
            $entities = array();
        }

        return $entities;
    }

    static private function mark_DE_vendor_exported($de_entity_id) {
        global $db;
        $sql = sprintf("UPDATE data_vendors
                        SET status ='exported'
                        WHERE id = %d", $de_entity_id);
        $db->query($sql);
    }

    static private function getDuplicates($name, $url, $type) {
        global $db;

        if ($type === 'vendor') {
            $table       = 'vendor';
            $id_column   = 'vendor_id';
            $name_column = 'vendor_name';
        } else {
            $table       = 'company';
            $id_column   = 'company_id';
            $name_column = 'company_name';
        }

        $query = sprintf("SELECT * FROM %s
                        WHERE status in ('active', 'review')
                        AND (%s = '%s' OR website = '%s')
                        ORDER BY %s
                        LIMIT 1", $table, $name_column, $name, $url, $id_column);

        $entities = $db->query($query);
        if (!$entities) {
            return array();
        }

        $duplicates = array();
        foreach ($entities as $entity) {
            $duplicate       = array();
            $duplicate['id'] = $entity[$id_column];
            if ($entity[$name_column] == $name) {
                $duplicate['dupl_type'] = 'name';
            } else {
                $duplicate['dupl_type'] = 'url';
            }
            $duplicates[] = $duplicate;
        }
        return $duplicates;
    }

    static private function backupEntity($entity_id, $type) {
        global $db;

        if ($type === 'vendor') {
            $entity      = new Vendor($entity_id);
            $name_column = 'vendor_name';
            $id_column   = 'vendor_id';
        } else {
            $entity      = new Vendor($entity_id);
            $name_column = 'company_name';
            $id_column   = 'company_id';
        }

        $data = $entity->get();

        $query = sprintf("INSERT INTO data_backup_vendors
                                    (vendor_id, type, vendor_name, code_name,
                                     logo_id, data_json)
                               VALUES (%d, '%s', '%s', '%s', %d, '%s')", $data[$id_column], $type, $db->escape_text($data[$name_column]), $db->escape_text($data['code_name']), $data['logo_id'], $db->escape_text(json_encode($data)));

        $db->query($query);
    }

    static private function prepare_entity_data($de_entity, $data = array()) {
        $f_update = false;
        if ($data) {
            $f_update = true;
        }

        if ($de_entity['type'] === 'vendor') {
            $name_column = 'vendor_name';
        } else {
            $name_column = 'company_name';
        }

        $locations = array();
        if ($de_entity['city']) {
            $locations[] = $de_entity['city'];
        }
        if ($de_entity['country']) {
            $locations[] = $de_entity['country'];
        }

        $data[$name_column] = $de_entity['vendor'];
        $data['status']     = 'review';

        if ($de_entity['description']) {
            $data['description'] = $de_entity['description'];
        }
        if ($de_entity['crawled_google_url']) {
            $data['website'] = $de_entity['crawled_google_url'];
        }
        if ($de_entity['twitter']) {
            $data['twitter'] = $de_entity['twitter'];
        }
        if ($de_entity['facebook']) {

            $data['facebook'] = $de_entity['facebook'];
        }
        if ($de_entity['linkedin']) {
            $data['linkedin'] = $de_entity['linkedin'];
        }
        if ($locations) {
            $data['location'] = implode(', ', $locations);
        }
        if ($f_update) {
            $data['date_modified'] = date("Y-m-d H:i:s");
        } else {
            $data['date_added'] = date("Y-m-d H:i:s");
        }

        if ($de_entity['type'] === 'company') {
            // todo bear add size and industry
            $temp_company = new Vendor();
            $right_size   = $temp_company->getProperSizeValue($de_entity['size']);
            if ($right_size) {
                $data['company_size'] = $right_size;
            }

            $industry_tag_id = $temp_company->getIndustryTagIdByName($de_entity['industry']);
            if ($industry_tag_id) {
                $data['tag_list']   = array();
                $data['tag_list'][] = $industry_tag_id;
            }
        }

        return $data;
    }

    static function import_vendors_from_DE($de_entities_ids) {
        $entities             = self::load_DE_entities($de_entities_ids);
        $results              = array();
        $results['vendors']   = array();
        $results['companies'] = array();
        $results['errors']    = array();

        $ref_host = '';
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            $ref_host         = $_SERVER['HTTP_ORIGIN'];
            // todo update after testing on beta
            $ref_host         = str_replace("http://", "http://aws:aw5@", $ref_host);
            $logos_url_prefix = "$ref_host/logos/";
        }

        Log::$logger->info("Started Import from DE. DE host = " . $ref_host . ", entities count = " . count($entities));

        foreach ($entities as $de_entity) {
            Log::$logger->info("Importing vendor from DE, name = " . $de_entity['vendor']);

            $data = array();

            $duplicates = self::getDuplicates($de_entity['vendor'], $de_entity['crawled_google_url'], $de_entity['type']);

            if ($duplicates) {
                // we will backup and update only the first duplicate found
                $first_duplicate = $duplicates[0];

                self::backupEntity($first_duplicate['id'], $de_entity['type']);
                error_log(sprintf("Found duplicate by %s for %s %s, VS id = %d, backed up.", $first_duplicate['dupl_type'], $de_entity['type'], $de_entity['vendor'], $first_duplicate['id']));

                // will update vendor instead of creating new
                if ($de_entity['type'] === 'vendor') {
                    $temp_entity = new Vendor($first_duplicate['id']);
                } else {
                    $temp_entity = new Vendor($first_duplicate['id']);
                }
                $data      = $temp_entity->get();
                $is_update = true;
            } else {
                if ($de_entity['type'] === 'vendor') {
                    $temp_entity = new Vendor();
                } else {
                    $temp_entity = new Vendor();
                }
                $is_update = false;
            }

            $data      = self::prepare_entity_data($de_entity, $data);
            $temp_entity->set($data);
            $entity_id = $temp_entity->save_data();

            // we need to reload $temp_vendor as it's vendor_id field is not updated currently on save_data(),
            // and without vendor_id in saveLogoByUrl() we can't save vendor correctly (will be calculated next code_name)
            unset($temp_entity);
            if ($de_entity['type'] === 'vendor') {
                $temp_entity = new Vendor($entity_id);
            } else {
                $temp_entity = new Vendor($entity_id);
            }

            if ($de_entity['logo_filename']) {
                if (!$ref_host) {
                    $results['errors'][] = $de_entity['vendor'] . ": logo is not saved as it is not ajax request.";
                } else {
                    $logo_url = $logos_url_prefix . $de_entity['logo_filename'];
                    $result   = $temp_entity->saveLogoByUrl($logo_url);
                    if ($result !== true) {
                        $results['errors'][] = $de_entity['vendor'] . "(" . $de_entity['type'] . "): " . $result;
                    } else {
                        Log::$logger->info("Logo from DE saved successfully, entity name = " . $de_entity['vendor']);
                    }
                }
            }

            if ($de_entity['type'] === 'vendor') {
                $array_key = "vendors";
            } else {
                $array_key = "companies";
            }

            $results[$array_key][$de_entity['id']]['status']         = 'ok';
            $results[$array_key][$de_entity['id']]['type']           = $de_entity['type'];
            $results[$array_key][$de_entity['id']]['code_name']      = $temp_entity->get_data('code_name');
            $results[$array_key][$de_entity['id']]['vs_vendor_id']   = $entity_id;
            $results[$array_key][$de_entity['id']]['de_vendor_name'] = $de_entity['vendor'];
            $results[$array_key][$de_entity['id']]['is_update']      = $is_update;

            self::mark_DE_vendor_exported($de_entity['id']);
            Log::$logger->info("Entity saved successfully, new code_name = " . $temp_entity->get_data('code_name'));
            unset($temp_entity);
        }
        return $results;
    }

}

?>
