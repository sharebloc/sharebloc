<?php

// todo bear I did not extend this class from BaseObject as it's not stable now, may be later we should do this
class Claim {

    const CLAIM_KEY_TIME_TO_LIVE_HOURS = 24;

    public static function insertClaim($user_id, $entity_id, $entity_type, $claim_key) {
        global $db;

        self::deletePreviousEntityClaims($entity_id, $entity_type);

        $query = sprintf("INSERT INTO claim (user_id, entity_id, entity_type, claim_key, created_ts)
                                VALUES (%d, %d, '%s', '%s', now())", $user_id, $entity_id, $entity_type, $claim_key);
        $db->query($query);
    }

    public static function deletePreviousEntityClaims($entity_id, $entity_type) {
        global $db;
        $query = sprintf("UPDATE claim SET deleted_ts=NOW()
                                WHERE deleted_ts IS NULL
                                AND entity_type = '%s'
                                AND entity_id=%d", $entity_type, $entity_id);
        $db->query($query);
    }

    public static function getActiveClaimByEntity($entity_id, $entity_type) {
        global $db;
        $query = sprintf("SELECT * FROM claim
                            WHERE deleted_ts IS NULL
                            AND entity_id=%d
                            AND entity_type='%s'
                            AND created_ts > NOW() - INTERVAL %d HOUR", $entity_id, $entity_type, self::CLAIM_KEY_TIME_TO_LIVE_HOURS);

        $result = $db->query($query);
        if ($result) {
            return $result[0];
        }
        return array();
    }

    public static function getDailyClaimsCount($entity_id, $entity_type) {
        global $db;
        // we should count deleted claims too, as claims are deleted on the new claim insert
        $query = sprintf("SELECT COUNT(1) as count FROM claim
                    WHERE entity_id=%d
                    AND entity_type='%s'
                    AND created_ts > NOW() - INTERVAL %d HOUR", $entity_id, $entity_type, self::CLAIM_KEY_TIME_TO_LIVE_HOURS);

        $result = $db->query($query);
        if ($result) {
            return intval($result[0]['count']);
        }
        return 0;
    }

    public static function getClaimsByUser($user_id) {
        global $db;
        $query = sprintf("SELECT * FROM claim
                    WHERE deleted_ts IS NULL
                    AND user_id=%d
                    AND created_ts > NOW() - INTERVAL %d HOUR", $user_id, self::CLAIM_KEY_TIME_TO_LIVE_HOURS);

        $result = $db->query($query);
        if ($result) {
            return $result;
        }
        return array();
    }

    public static function deleteUsersClaims($user_id) {
        global $db;

        if (!$user_id) {
            return;
        }

        $query = sprintf("UPDATE claim
                            SET deleted_ts = now()
                            WHERE user_id=%d
                            AND deleted_ts IS NULL", $user_id);

        $db->query($query);
        return;
    }

    public static function getClaimByKey($claim_key) {
        global $db;
        $query = sprintf("SELECT * FROM claim
                            WHERE deleted_ts IS NULL
                            AND claim_key='%s'
                            AND created_ts > NOW() - INTERVAL %d HOUR", $claim_key, self::CLAIM_KEY_TIME_TO_LIVE_HOURS);

        $result = $db->query($query);
        if ($result) {
            return $result[0];
        }
        return array();
    }

    public static function processClaimKey($claim_key) {
        $result = false;
        $claim  = self::getClaimByKey($claim_key);
        if (!$claim) {
            return false;
        }

        // so we can approve this claim
        if ('vendor' === $claim['entity_type']) {
            $vendor = new Vendor($claim['entity_id']);
            $result = $vendor->approveClaim($claim['user_id']);
        }

        self::deletePreviousEntityClaims($claim['entity_id'], $claim['entity_type']);
        return $claim['entity_id'];
    }

}

?>