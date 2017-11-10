<?php

require_once __DIR__."/lib/GPResource.php";
require_once __DIR__."/lib/GPResourceInfo.php";

class GebaeudeplaenePlugin extends StudIPPlugin implements SystemPlugin
{
    public function __construct() {
        parent::__construct();
        $nav = new Navigation(_("Geb�udepl�ne"), PluginEngine::getURL($this, array(), "tree"));
        if ($GLOBALS['perm']->have_perm("admin")) {
            $nav->setImage(Assets::image_path("icons/28/lightblue/institute"));
        }
        $tree = new Navigation(_("Geb�udebaum"), PluginEngine::getURL($this, array(), "tree"));
        $nav->addSubNavigation("tree", $tree);
        Navigation::addItem("/gebaeudeplaene", $nav);
    }
}