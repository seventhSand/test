<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 11/29/2016
 * Time: 12:51 PM
 */

return [
        ['master' => 'id'],
        ['master' => 'uShortLabel', 'name' => 'username'],
        ['master' => 'label', 'name' => 'password'],
        ['master' => 'uLabel', 'name' => 'email'],
        ['master' => 'falseBool', 'name' => 'is_system'],
        ['master' => 'bool', 'name' => 'is_active'],
// This table has create & edit timestamp column. Add it automatically
        'timestamps' => true,
// For log admin
        'history' => [
                'object' => 'username'
        ]
];