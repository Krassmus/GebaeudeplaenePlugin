<?php

require_once __DIR__."/lib/GPResource.php";
require_once __DIR__."/lib/GPResourceInfo.php";

class GebaeudeplaenePlugin extends StudIPPlugin implements SystemPlugin
{
    public function __construct() {
        parent::__construct();
        $nav = new Navigation(_("Gebäudepläne"), PluginEngine::getURL($this, array(), "tree"));
        if ($GLOBALS['perm']->have_perm("admin")) {
            $nav->setImage(Icon::create("institute", "navigation"));
        }
        $tree = new Navigation(_("Gebäudebaum"), PluginEngine::getURL($this, array(), "tree"));
        $nav->addSubNavigation("tree", $tree);
        Navigation::addItem("/gebaeudeplaene", $nav);
    }
}