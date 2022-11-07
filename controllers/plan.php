<?php

class PlanController extends PluginController {

    public function index_action()
    {
        Navigation::activateItem("/gebaeudeplaene/tree");
        PageLayout::setTitle(_("Gebäudeplan"));
        PageLayout::addScript($this->plugin->getPluginURL()."/assets/gebaeudeplan.js");
        PageLayout::addStylesheet($this->plugin->getPluginURL()."/assets/gebaeudeplan.css");
        $this->resource = Resource::find(Request::option("resource_id"));
        $resource_ids = array($this->resource->getId());
        do {
            $oldcount = count($resource_ids);
            $statement = DBManager::get()->prepare("
                SELECT id
                FROM resources
                WHERE parent_id IN (:resource_ids)
            ");
            $statement->execute(array('resource_ids' => $resource_ids));
            $resource_ids = array_unique(array_merge($resource_ids, $statement->fetchAll(PDO::FETCH_COLUMN, 0)));
        } while (count($resource_ids) !== $oldcount);

        $more = "";

        if (Request::get("free")) {
            $more .= "
                UNION /* Buchungen ohne Veranstaltungs- oder Personenbezug */
                (
                    SELECT IFNULL(resource_booking_intervals.`begin`, resource_bookings.`begin`) AS `begin`,
                           IFNULL(resource_booking_intervals.`end`, resource_bookings.`end`) AS `end`,
                           resource_bookings.description AS name,
                           '0' AS is_ex_termin,
                           '' AS `dozenten`,
                           resources.name AS `room`
                    FROM resource_bookings
                        INNER JOIN resources ON (resources.id = resource_bookings.resource_id)
                        LEFT JOIN resource_booking_intervals ON (resource_booking_intervals.booking_id = resource_bookings.id)
                    WHERE resource_bookings.resource_id IN (:resource_ids)
                        AND resource_bookings.range_id IS NULL
                        AND (
                            (resource_booking_intervals.`begin` IS NULL AND resource_bookings.`begin` < UNIX_TIMESTAMP() + 43200 AND resource_bookings.`begin` > UNIX_TIMESTAMP() - :hide_after_time AND resource_bookings.`end` > UNIX_TIMESTAMP())
                            OR (resource_booking_intervals.`begin` < UNIX_TIMESTAMP() + 43200 AND resource_booking_intervals.`begin` > UNIX_TIMESTAMP() - :hide_after_time AND resource_booking_intervals.`end` > UNIX_TIMESTAMP())
                        )
                )
                UNION /* Buchungen nur mit Personenbezug */
                (
                    SELECT IFNULL(resource_booking_intervals.`begin`, resource_bookings.`begin`) AS `begin`,
                           IFNULL(resource_booking_intervals.`end`, resource_bookings.`end`) AS `end`,
                           resource_bookings.description AS name,
                           '0' AS is_ex_termin,
                           auth_user_md5.user_id AS `dozenten`,
                           resources.name AS `room`
                    FROM resource_bookings
                        LEFT JOIN resource_booking_intervals ON (resource_booking_intervals.booking_id = resource_bookings.id)
                        INNER JOIN resources ON (resources.id = resource_bookings.resource_id)
                        INNER JOIN auth_user_md5 ON (auth_user_md5.user_id = resource_bookings.range_id)
                    WHERE resource_bookings.resource_id IN (:resource_ids)
                        AND (
                            (resource_booking_intervals.`begin` IS NULL AND resource_bookings.`begin` < UNIX_TIMESTAMP() + 43200 AND resource_bookings.`begin` > UNIX_TIMESTAMP() - :hide_after_time AND resource_bookings.`end` > UNIX_TIMESTAMP())
                            OR (resource_booking_intervals.`begin` < UNIX_TIMESTAMP() + 43200 AND resource_booking_intervals.`begin` > UNIX_TIMESTAMP() - :hide_after_time AND resource_booking_intervals.`end` > UNIX_TIMESTAMP())
                        )
                )
            ";
        }

        if (Request::get("free")) {
            $more .= "
                UNION ( /* termine ohne Räume */
                    SELECT
                        termine.`date` AS `begin`,
                        termine.`end_time` AS `end`,
                        seminare.name AS name,
                        '0' AS is_ex_termin,
                        IFNULL (GROUP_CONCAT(termin_related_persons.user_id ORDER BY seminar_user.position ASC SEPARATOR ','), GROUP_CONCAT(seminar_user.user_id ORDER BY seminar_user.position ASC SEPARATOR ',')) AS `dozenten`,
                        termine.raum AS `room`
                    FROM termine
                        INNER JOIN seminare ON (seminare.Seminar_id = termine.range_id)
                        INNER JOIN seminar_user ON (seminare.Seminar_id = seminar_user.Seminar_id AND seminar_user.status = 'dozent')
                        LEFT JOIN termin_related_persons ON (termin_related_persons.range_id = termine.termin_id AND termin_related_persons.user_id = seminar_user.user_id)
                    WHERE termine.end_time > UNIX_TIMESTAMP()
                        AND termine.date < UNIX_TIMESTAMP() + 43200
                        AND termine.date > UNIX_TIMESTAMP() - :hide_after_time
                        AND termine.raum IS NOT NULL
                        AND termine.raum != ''
                )
                UNION ( /* ex_termine ohne Räume */
                    SELECT
                        ex_termine.`date` AS `begin`,
                        ex_termine.`end_time` AS `end`,
                        seminare.name AS name,
                        '1' AS is_ex_termin,
                        GROUP_CONCAT(seminar_user.user_id ORDER BY seminar_user.position ASC SEPARATOR ',') AS `dozenten`,
                        ex_termine.raum AS `room`
                    FROM ex_termine
                        INNER JOIN seminare ON (seminare.Seminar_id = ex_termine.range_id)
                        INNER JOIN seminar_user ON (seminare.Seminar_id = seminar_user.Seminar_id AND seminar_user.status = 'dozent')
                    WHERE ex_termine.end_time > UNIX_TIMESTAMP()
                        AND ex_termine.date < UNIX_TIMESTAMP() + 43200
                        AND ex_termine.date > UNIX_TIMESTAMP() - :hide_after_time
                        AND ex_termine.raum IS NOT NULL
                        AND ex_termine.raum != ''
                )
            ";
        }


        $statement = DBManager::get()->prepare("
            (   /* termine */
                SELECT termine.`date` AS `begin`,
                       termine.`end_time` AS `end`,
                       seminare.name AS name,
                       '0' AS is_ex_termin,
                       IFNULL (GROUP_CONCAT(termin_related_persons.user_id ORDER BY seminar_user.position ASC SEPARATOR ','), GROUP_CONCAT(seminar_user.user_id ORDER BY seminar_user.position ASC SEPARATOR ',')) AS `dozenten`,
                       resources.name AS `room`
                FROM termine
                    INNER JOIN resource_bookings ON (termine.termin_id = resource_bookings.range_id)
                    INNER JOIN resources ON (resources.id = resource_bookings.resource_id)
                    INNER JOIN seminare ON (seminare.Seminar_id = termine.range_id)
                    INNER JOIN seminar_user ON (seminare.Seminar_id = seminar_user.Seminar_id AND seminar_user.status = 'dozent')
                    LEFT JOIN termin_related_persons ON (termin_related_persons.range_id = termine.termin_id AND termin_related_persons.user_id = seminar_user.user_id)
                WHERE resource_bookings.resource_id IN (:resource_ids)
                    AND termine.end_time > UNIX_TIMESTAMP()
                    AND termine.date < UNIX_TIMESTAMP() + 43200
                    AND termine.date > UNIX_TIMESTAMP() - :hide_after_time
                GROUP BY termine.termin_id
            )
            UNION /* ex_termine */
            (
                SELECT ex_termine.`date` AS `begin`, ex_termine.`end_time` AS `end`, seminare.name AS name,
                       '1' AS is_ex_termin,
                       GROUP_CONCAT(seminar_user.user_id ORDER BY seminar_user.position ASC SEPARATOR ',') AS `dozenten`,
                       resources.name AS `room`
                FROM ex_termine
                    INNER JOIN resources ON (resources.id = ex_termine.resource_id)
                    INNER JOIN seminare ON (seminare.Seminar_id = ex_termine.range_id)
                    INNER JOIN seminar_user ON (seminare.Seminar_id = seminar_user.Seminar_id AND seminar_user.status = 'dozent')
                WHERE ex_termine.resource_id IN (:resource_ids)
                    AND ex_termine.end_time > UNIX_TIMESTAMP()
                    AND ex_termine.date < UNIX_TIMESTAMP() + 43200
                    AND ex_termine.date > UNIX_TIMESTAMP() - :hide_after_time
                GROUP BY ex_termine.termin_id
            )
            ".$more."
            ORDER BY `begin` ASC
        ");
        $statement->execute(array(
            'resource_ids' => $resource_ids,
            'hide_after_time' => Config::get()->GEBAEUDEPLAENE_HIDE_DATES_AFTER_TIME
        ));
        $this->dates = array();

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $data) {
            if ($data['begin']) {
                $this->dates[] = $data;
            }
        }
    }

    public function kiosk_action()
    {
        $this->index_action();
        $tf = new Flexi_TemplateFactory(__DIR__."/../views");
        $this->set_layout($tf->open("plan/kiosk_layout.php"));
        $this->render_action("_gebaeudeplan");
    }

    public function get_update_action()
    {
        $this->set_layout(null);
        $this->index_action();
        $this->render_template("plan/_gebaeudeplan");
    }

}
