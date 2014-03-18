<?php

require_once('class.BaseObject.php');
require_once('class.User.php');


class Event {

    const EVENTS_ON_PAGE = 20;

    static $no_more_content = 0;

    public static function insertEvent($data) {

        $data['user_id'] = get_user_id();

        $query = sprintf("INSERT INTO calendar
                                (name, url, start_date, end_date,
                                    location, tag_id, user_id, added_ts)
                                VALUES
                                ('%s', '%s', %s, %s,
                                '%s', %d, %d, now())",

                                Database::escapeString($data['name']),
                                Database::escapeString($data['url']),
                                $data['start_date'] ? "'".Database::escapeString($data['start_date'])."'" : 'null',
                                $data['end_date'] ? "'".Database::escapeString($data['end_date'])."'" : 'null',

                                Database::escapeString($data['location']),
                                $data['tag_id'],
                                $data['user_id']);
        Database::exec($query);
    }

    public static function updateEvent($data) {
        $query = sprintf("UPDATE calendar
                            SET name = '%s',
                                url = '%s',
                                start_ts  = %s,
                                end_ts  = %s,
                                location = '%s',
                                f_approved = 1,
                                f_only_month = %d
                            WHERE event_id = %d;",

                            Database::escapeString($data['name']),
                            Database::escapeString($data['url']),
                            $data['start_date'] ? "'".Database::escapeString($data['start_date'])."'" : 'null',
                            $data['end_date'] ? "'".Database::escapeString($data['end_date'])."'" : 'null',
                            Database::escapeString($data['location']),
                            $data['f_only_month'],
                            $data['event_id']
                        );
        Database::exec($query);

        self::updateEventSubtag($data);
        self::updateEventRelation($data);
    }

    public static function deleteEvent($event_id) {
        $query = sprintf("UPDATE calendar SET deleted_ts = NOW()
                            WHERE event_id = %d;",
                            $event_id);
        Database::exec($query);
    }

    public static function getEventById($event_id) {
        $query = sprintf("SELECT * FROM calendar
                            WHERE deleted_ts IS NULL
                                AND event_id = %d;",
                        $event_id);
        $event = Database::execArray($query, true);
        if (!$event) {
            return array();
        }

        $event['entity_type'] = 'event';
        $event['post_type'] = 'event';
        $event['uid'] = $event['post_type'] . '_' . $event['event_id'];

        $query = sprintf("SELECT tag_id FROM tag_selection
                            WHERE tag_type='vendor'
                                AND entity_type='event'
                                AND entity_id=%d",
                            $event['event_id']
                        );
        $tag_selected = Database::execArray($query, true);

        $event['subtag_id'] = $tag_selected ? $tag_selected['tag_id'] : null;

        $query = sprintf("SELECT v.vendor_id, v.vendor_name
                            FROM vendor v
                            WHERE v.vendor_id IN (SELECT vendor_id FROM relation
                                                    WHERE entity_type='event'
                                                        AND entity_id=%d)",
                            $event['event_id']
                        );
        $vendors = Database::execArray($query);

        $event['vendors'] = $vendors;

        return $event;

    }

    public static function getEvents($page_tag_id, $limit = self::EVENTS_ON_PAGE, $offset = 0) {
        $is_admin = is_admin();

        if (!$page_tag_id) {
            Log::$logger->warn("Cannot get events from DB: empty page_tag_id.");
            return array();
        }

        $limit = $limit + 1;

        $unapproved_events_sql = '(SELECT * FROM
                                    (SELECT *, 0 AS f_old FROM calendar
                                        WHERE deleted_ts IS NULL
                                            AND f_approved = 0
                                    ) AS unapproved_events
                                )
                                UNION';

        $query = sprintf('SELECT * FROM
                        (
                            %1$s
                            (SELECT * FROM
                                    (SELECT *, 0 AS f_old FROM calendar
                                        WHERE deleted_ts IS NULL
                                            AND (start_ts >= NOW()
                                                OR end_ts >= NOW())
                                            AND f_approved = 1
                                        ORDER BY start_ts asc, end_ts asc
                                    ) AS approved_next_events
                                )
                                UNION
                            (SELECT * FROM
                                (SELECT *, 0 AS f_old FROM calendar
                                    WHERE deleted_ts IS NULL
                                        AND start_ts is NULL
                                        AND f_approved = 1
                                ) AS approved_wo_date_events
                            )
                            UNION
                            (SELECT * FROM
                                (SELECT *, 1 AS f_old FROM calendar
                                    WHERE start_ts < NOW()
                                        AND f_approved = 1
                                        ORDER BY start_ts desc, end_ts desc
                                ) AS approved_expired_events
                            )
                        ) as result
                        WHERE deleted_ts IS NULL
                            AND tag_id=%2$d
                        LIMIT %3$d, %4$d;
                        ',
                $is_admin ? $unapproved_events_sql : '',
                $page_tag_id,
                $offset,
                $limit
                );

        $events = Database::execArray($query);

        if (count($events) < $limit) {
            self::$no_more_content = 1;
        } else {
            // as we got limit+1 rows
            array_pop($events);
        }

        if (!$events) {
            return $events;
        }

        foreach ($events as &$event) {
            $event['entity_type'] = 'event';
            $event['post_type'] = 'event';
            $event['uid'] = $event['post_type'] . '_' . $event['event_id'];
        }

        return $events;
    }

    static function getEventsForMore($page_tag_id, $offset) {

        $content = self::getEvents($page_tag_id, self::EVENTS_ON_PAGE, $offset);
        if (!$content) {
            return false;
        }

        $html_divs = array();
        Utils::$smarty->assign('date_format', "%b %d");

        foreach ($content as $event) {
            Utils::$smarty->assign('event', $event);
            $html_divs[] = Utils::$smarty->fetch('components/front/event.tpl');
        }

        $result = array();
        $result['html_divs'] = $html_divs;
        $result['no_more_content'] = self::$no_more_content;
        $result['offset_for_next_query'] = $offset + self::EVENTS_ON_PAGE;

        return $result;
    }

    public static function getPostDataFromRequest() {
        $data = array();
        $data['event_id'] = trim(get_input('event_id'));
        $data['name'] = trim(get_input('name'));
        $data['url'] = trim(get_input('url'));
        $data['start_date'] = trim(get_input('start_date'));
        $data['end_date'] = trim(get_input('end_date'));
        $data['location'] = trim(get_input('location'));
        $data['month'] = trim(get_input('month'));
        $data['tag_id'] = trim(get_input('tag_id'));
        $data['f_only_month'] = 0;

        $data['subtag_id'] = trim(get_input('subtag_id'));
        $data['vendors'] = get_input('vendors');


        return $data;
    }

    public static function processPostedEvent($data = array()) {
        if(!$data) {
            $data = self::getPostDataFromRequest();
        }

        if (!$data['name']) {
            return "Field Event Name is required";
        }

        if ($data['url'] && !Utils::validate_url($data['url'])) {
            return "You must enter a valid URL or leave this field empty.";
        }

        if (!empty($data['event_id'])) {

            if ($data['start_date']) {
                if (!Utils::isMysqlFormattedDate($data['start_date'])) {
                    return "Wrong format of Start Date value";
                }

                if (!empty($data['month'])) {
                    $data['month'] = "";
                    return "Leave Month field is empty - you have already entered Start Date value.";
                }
            } else {
                if (!empty($data['month'])) {
                    $month_num = intval($data['month']);

                    if ($month_num >= 1 && $month_num <= 12 &&
                            sprintf("%02d", $month_num) === $data['month']) {

                        $data['start_date'] = sprintf("%d-%02d-01 00:00:00",
                                                        date("Y"),
                                                        $month_num);
                        $data['f_only_month'] = 1;
                    } else {
                        return "Wrong format of Month Field value";
                    }
                }
            }

            if ($data['end_date'] && !Utils::isMysqlFormattedDate($data['end_date'])) {
                return "Wrong format of End Date value";
            }

            if($data['start_date'] && $data['end_date'] &&
                    strtotime($data['start_date']) > strtotime($data['end_date'])) {
                return "End Date can't be before the Start Date";
            }

            if(empty($data['start_date']) && $data['end_date']) {
                return "You have to fill in Start Date value before End Date";
            }

            // todo review - doesn't work correctly
            if ($data['subtag_id'] == 'null') {
                return "You have to choose a Tag.";
            }
        }

        if (!$data['location']) {
            return "Field Location is required";
        }

        if (empty($data['event_id'])) {
            self::insertEvent($data);
        } else {
            self::updateEvent($data);
        }
        return true;

    }

    private static function updateEventSubtag($data) {
        $query = sprintf("DELETE FROM tag_selection
                            WHERE entity_id=%d
                                AND entity_type='event';",
                            $data['event_id']
                    );
        Database::exec($query);

        if (!$data['subtag_id']) {
            return;
        }

        $query = sprintf("INSERT INTO tag_selection (entity_id, entity_type, tag_id)
                            VALUES (%d, 'event', %d)",
                            $data['event_id'], $data['subtag_id']);
        Database::exec($query);
    }

    private static function updateEventRelation($data) {
        $query = sprintf("DELETE FROM relation
                            WHERE entity_id=%d
                                AND entity_type='event';",
                            $data['event_id']
                    );
        Database::exec($query);

        if (!$data['vendors']) {
            return;
        }

        foreach ($data['vendors'] as $vendor_id) {
            $query = sprintf("INSERT INTO relation (entity_id, entity_type, vendor_id)
                                VALUES (%d, 'event', %d)",
                                $data['event_id'], $vendor_id);
            Database::exec($query);
        }
    }
}