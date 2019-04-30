<?php

class GPResourceInfo extends SimpleORMap
{
    protected static function configure($config = array())
    {
        $config['db_table'] = 'gebaeudeplan_infos';
        $config['belongs_to']['resource'] = array(
            'class_name' => 'GPResource',
            'foreign_key' => 'resource_id'
        );
        parent::configure($config);
    }
}