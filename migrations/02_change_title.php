<?php

class ChangeTitle extends Migration {
    
	public function up() {
	    DBManager::get()->exec("
	        ALTER TABLE `gebaeudeplan_infos` 
	        ADD COLUMN `title` varchar(32) NULL AFTER `top_info`
	    ");
	    SimpleORMap::expireTableScheme();
	}
	
	public function down() {
        DBManager::get()->exec("
            ALTER TABLE `gebaeudeplan_infos`
            DROP COLUMN `title`
        ");
        SimpleORMap::expireTableScheme();
	}
}