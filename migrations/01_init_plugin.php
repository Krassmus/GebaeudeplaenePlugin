<?php

class InitPlugin extends Migration {
    
	public function up() {
	    DBManager::get()->exec("
	        CREATE TABLE IF NOT EXISTS `gebaeudeplan_infos` (
                `resource_id` varchar(32) NOT NULL DEFAULT '',
                `top_info` text,
                `bottom_info` text,
                `side_info` text,
                `mkdate` int(11) DEFAULT NULL,
                `chdate` int(11) DEFAULT NULL,
                PRIMARY KEY (`resource_id`)
            ) ENGINE=InnoDB
	    ");
	}
	
	public function down() {
        DBManager::get()->exec("
            DROP TABLE IF EXISTS `gebaeudeplan_infos`
        ");
	}
}