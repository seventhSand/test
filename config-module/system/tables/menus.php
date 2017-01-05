<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/14/2016
 * Time: 10:11 AM
 */

return [
        ['master' => 'id'],
        ['master' => 'int', 'name' => 'parent_id'],
        ['master' => 'label', 'name' => 'title', 'multilingual' => true],
        ['master' => 'uLongLabel', 'name' => 'permalink'],
        ['master' => 'longLabel', 'name' => 'external_link', 'notnull' => false],
        ['master' => 'label', 'name' => 'template'],
        ['master' => 'falseBool', 'name' => 'is_active'],
        ['master' => 'falseBool', 'name' => 'is_system'],
        ['master' => 'sequence'],
        'timestamps' => true,
        'history' => [
                'group' => 'menu',
                'object' => 'title'
        ]
];