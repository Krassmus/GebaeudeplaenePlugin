<?php

class GPResource extends SimpleORMap
{
    protected static function configure($config = array())
    {
        $config['db_table'] = 'resources_objects';
        $config['belongs_to']['parent'] = array(
            'class_name' => 'GPResource',
            'foreign_key' => 'parent_id'
        );
        parent::configure($config);
    }
}