<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 11/29/2016
 * Time: 1:40 PM
 */

return [
        ['master' => 'id'],
        ['master' => 'int', 'name' => 'admin_id', 'reference' => 'admins.id', 'uniques' => true],
        ['master' => 'int', 'name' => 'role_id', 'uniques' => true],
        'history' => [
                'insert' => ['assigned', 'roles.title', 'admins.username'],
                'update' => ['unsigned', 'roles.title', 'admins.username']
        ]
];