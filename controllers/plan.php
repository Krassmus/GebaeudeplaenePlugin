<?php

require_once 'app/controllers/plugin_controller.php';

class PlanController extends PluginController {

    public function index_action() {
        Navigation::activateItem("/gebaeudeplaene/tree");
        $this->resource = GPResource::find(Request::option("resource_id"));
        $statement = DBManager::get()->prepare("
            SELECT termine.* 
            FROM resources_assign
                INNER JOIN termine ON (termine.range_id = resources_assign.assign_user_id)
            WHERE resources_assign.resource_id = :resource_id
                AND termine.date > UNIX_TIMESTAMP()
                AND termine.date < UNIX_TIMESTAMP() + 43200
            ORDER BY termine.date ASC
        ");
        $statement->execute(array(
            'resource_id' => Request::option("resource_id")
        ));
        $this->dates = array();
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $data) {
            $this->dates[] = CourseDate::buildExisting($data);
        }
    }
}