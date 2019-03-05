<?php

require_once 'app/controllers/plugin_controller.php';

class InfoController extends PluginController
{
    public function edit_action($resource_id) {
        Navigation::activateItem("/gebaeudeplaene/tree");
        PageLayout::setTitle(_("Seiteninformationen zum Plan"));
        $this->resource = GPResource::find($resource_id);
        $this->info = new GPResourceInfo($resource_id);
        if (Request::isPost()) {
            $this->info['top_info'] = Request::get("top_info");
            $this->info['title'] = Request::get("title") ?: null;
            $this->info['side_info'] = Request::get("side_info");
            $this->info['bottom_info'] = Request::get("bottom_info");
            $this->info->store();
            PageLayout::postMessage(MessageBox::success(_("Daten wurden erfolgreich gespeichert")));
        }
    }

}