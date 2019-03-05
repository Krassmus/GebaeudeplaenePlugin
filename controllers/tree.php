<?php

require_once 'app/controllers/plugin_controller.php';

class TreeController extends PluginController {

    public function index_action() {
        Navigation::activateItem("/gebaeudeplaene/tree");
        PageLayout::setTitle(_("Gebäudeplan"));
        $this->resources = GPResource::findBySQL("parent_id = ? ORDER BY name ASC", array(Request::option("resource_id", "0")));
    }
}