<?php

require_once 'app/controllers/plugin_controller.php';

class PlanController extends PluginController {

    public function index_action() {
        Navigation::activateItem("/gebaeudeplaene/tree");
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
        $statement = DBManager::get()->prepare("
            SELECT termine.* 
            FROM resources_assign
                INNER JOIN termine ON (termine.termin_id = resources_assign.assign_user_id)
            WHERE resources_assign.resource_id IN (:resource_ids)
                AND termine.end_time > UNIX_TIMESTAMP()
                AND termine.date < UNIX_TIMESTAMP() + 43200
            ORDER BY termine.date ASC
        ");
        $statement->execute(array(
            'resource_ids' => $resource_ids
        ));
        $this->dates = array();
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $data) {
            $this->dates[] = CourseDate::buildExisting($data);
        }
    }

    public function get_update_action() {
        $this->set_layout(null);
        $this->index_action();
        $this->render_template("plan/_gebaeudeplan");
    }
}