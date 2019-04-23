<?php

require_once 'app/controllers/plugin_controller.php';

class PlanController extends PluginController {

    public function index_action()
    {
        Navigation::activateItem("/gebaeudeplaene/tree");
        PageLayout::setTitle(_("Gebäudeplan"));
        PageLayout::addScript($this->plugin->getPluginURL()."/assets/gebaeudeplan.js");
        PageLayout::addStylesheet($this->plugin->getPluginURL()."/assets/gebaeudeplan.css");
        $this->resource = GPResource::find(Request::option("resource_id"));
        $resource_ids = array($this->resource->getId());
        do {
            $oldcount = count($resource_ids);
            $statement = DBManager::get()->prepare("
                SELECT resource_id 
                FROM resources_objects
                WHERE parent_id IN (:resource_ids)
            ");
            $statement->execute(array('resource_ids' => $resource_ids));
            $resource_ids = array_unique(array_merge($resource_ids, $statement->fetchAll(PDO::FETCH_COLUMN, 0)));
        } while (count($resource_ids) !== $oldcount);

        if (Request::get("free")) {
            $freieangaben = "
                UNION (
                    SELECT
                        termine.`date` AS `begin`, 
                        termine.`end_time` AS `end`, 
                        seminare.name AS name, 
                        '0' AS is_ex_termin, 
                        IF(termin_related_persons.user_id IS NULL, GROUP_CONCAT(seminar_user.user_id ORDER BY seminar_user.position ASC SEPARATOR ','), GROUP_CONCAT(termin_related_persons.user_id ORDER BY seminar_user.position ASC SEPARATOR ',')) AS `dozenten`, 
                        termine.raum AS `room` 
                    FROM termine
                        INNER JOIN seminare ON (seminare.Seminar_id = termine.range_id)
                        INNER JOIN seminar_user ON (seminare.Seminar_id = seminar_user.Seminar_id AND seminar_user.status = 'dozent')
                        LEFT JOIN termin_related_persons ON (termin_related_persons.range_id = termine.termin_id AND termin_related_persons.user_id = seminar_user.user_id)
                    WHERE termine.end_time > UNIX_TIMESTAMP()
                        AND termine.date < UNIX_TIMESTAMP() + 43200
                        AND termine.raum IS NOT NULL 
                        AND termine.raum != ''
                )
                UNION (
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
                        AND ex_termine.raum IS NOT NULL 
                        AND ex_termine.raum != ''
                )
            ";
        }
        $statement = DBManager::get()->prepare("
            (
                SELECT termine.`date` AS `begin`, 
                       termine.`end_time` AS `end`, 
                       seminare.name AS name, 
                       '0' AS is_ex_termin, 
                       IF(termin_related_persons.user_id IS NULL, GROUP_CONCAT(seminar_user.user_id ORDER BY seminar_user.position ASC SEPARATOR ','), GROUP_CONCAT(termin_related_persons.user_id ORDER BY seminar_user.position ASC SEPARATOR ',')) AS `dozenten`, 
                       resources_objects.name AS `room`
                FROM termine
                    INNER JOIN resources_assign ON (termine.termin_id = resources_assign.assign_user_id)
                    INNER JOIN resources_objects ON (resources_objects.resource_id = resources_assign.resource_id)
                    INNER JOIN seminare ON (seminare.Seminar_id = termine.range_id)
                    INNER JOIN seminar_user ON (seminare.Seminar_id = seminar_user.Seminar_id AND seminar_user.status = 'dozent')
                    LEFT JOIN termin_related_persons ON (termin_related_persons.range_id = termine.termin_id AND termin_related_persons.user_id = seminar_user.user_id)
                WHERE resources_assign.resource_id IN (:resource_ids)
                    AND termine.end_time > UNIX_TIMESTAMP()
                    AND termine.date < UNIX_TIMESTAMP() + 43200
                GROUP BY termine.termin_id
            )
            UNION /* ex_termine */
            (
                SELECT ex_termine.`date` AS `begin`, ex_termine.`end_time` AS `end`, seminare.name AS name, 
                       '1' AS is_ex_termin, 
                       GROUP_CONCAT(seminar_user.user_id ORDER BY seminar_user.position ASC SEPARATOR ',') AS `dozenten`, 
                       resources_objects.name AS `room`
                FROM ex_termine
                    INNER JOIN resources_objects ON (resources_objects.resource_id = ex_termine.resource_id)
                    INNER JOIN seminare ON (seminare.Seminar_id = ex_termine.range_id)
                    INNER JOIN seminar_user ON (seminare.Seminar_id = seminar_user.Seminar_id AND seminar_user.status = 'dozent')
                WHERE ex_termine.resource_id IN (:resource_ids)
                    AND ex_termine.end_time > UNIX_TIMESTAMP()
                    AND ex_termine.date < UNIX_TIMESTAMP() + 43200
                GROUP BY ex_termine.termin_id
            )
            UNION /* Buchungen ohne Veranstaltungs- oder Personenbezug */
            (
                SELECT IFNULL(resources_temporary_events.`begin`, resources_assign.`begin`) AS `begin`, 
                       IFNULL(resources_temporary_events.`end`, resources_assign.`end`) AS `end`, 
                       resources_assign.user_free_name AS name, 
                       '0' AS is_ex_termin, 
                       '' AS `dozenten`, 
                       resources_objects.name AS `room`
                FROM resources_assign 
                    INNER JOIN resources_objects ON (resources_objects.resource_id = resources_assign.resource_id)
                    LEFT JOIN resources_temporary_events ON (resources_temporary_events.assign_id = resources_assign.assign_id)
                WHERE resources_assign.resource_id IN (:resource_ids)
                    AND resources_assign.assign_user_id IS NULL
                    AND (
                        (resources_temporary_events.`begin` IS NULL AND resources_assign.`begin` < UNIX_TIMESTAMP() + 43200 AND resources_assign.`end` > UNIX_TIMESTAMP())
                        OR (resources_temporary_events.`begin` < UNIX_TIMESTAMP() + 43200 AND resources_temporary_events.`end` > UNIX_TIMESTAMP())
                    )
                    
            )
            UNION /* Buchungen nur mit Personenbezug */
            (
                SELECT IFNULL(resources_temporary_events.`begin`, resources_assign.`begin`) AS `begin`, 
                       IFNULL(resources_temporary_events.`end`, resources_assign.`end`) AS `end`, 
                       resources_assign.user_free_name AS name,  
                       '0' AS is_ex_termin, 
                       auth_user_md5.user_id AS `dozenten`, 
                       resources_objects.name AS `room`
                FROM resources_assign 
                    LEFT JOIN resources_temporary_events ON (resources_temporary_events.assign_id = resources_assign.assign_id)
                    INNER JOIN resources_objects ON (resources_objects.resource_id = resources_assign.resource_id)
                    INNER JOIN auth_user_md5 ON (auth_user_md5.user_id = resources_assign.assign_user_id)
                WHERE resources_assign.resource_id IN (:resource_ids)
                    AND (
                        (resources_temporary_events.`begin` IS NULL AND resources_assign.`begin` < UNIX_TIMESTAMP() + 43200 AND resources_assign.`end` > UNIX_TIMESTAMP())
                        OR (resources_temporary_events.`begin` < UNIX_TIMESTAMP() + 43200 AND resources_temporary_events.`end` > UNIX_TIMESTAMP())
                    )
            )
            ".$freieangaben."
            ORDER BY `begin` ASC
        ");
        $statement->execute(array(
            'resource_ids' => $resource_ids
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

    public function get_update_action() {
        $this->set_layout(null);
        $this->index_action();
        $this->render_template("plan/_gebaeudeplan");
    }
}