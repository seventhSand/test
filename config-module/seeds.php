<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/15/2016
 * Time: 3:23 PM
 */

return [
        'configurations' => [
                [
                        'id' => 1,
// hash('sha1', 'redAlderGreatDane')
                        'key' => '48d35125f4a3c2c005d5b0697463c4651704b427',
// Crypt::encrypt(['name' => 'Dba', 'email' => 'zoidsimangunsong@gmail.com', 'secret' => 'rubik-cube', 'daemon' => true])
                        'setting' => 'eyJpdiI6IkIrakorZmc0aXhCYjhwSTg5cHhWOFE9PSIsInZhbHVlIjoiS0NibVBuSUpOeWxWWk8zNnV'
                                . 'oS1pZRkZXQVJpY3NxNllDUWlwNTVGb3dyWUt2XC9GOXZQMmJxQVF6cThWcWxPQitUU25ObHRCQnFjNUw1S1'
                                . 'BCZ2JTZVFTNE8xSWtPXC9pbFNncjJLN3R1bmFlWFlqQnoyNkRzRzBvSUpzVHZoNkpoWmRsSGtORytZXC9JR'
                                . 'kw2N1I2bEdueEJOR0lFNjRUWGc5U2FZYk1jME9nb1E4PSIsIm1hYyI6ImJhMWJjMjM1ZWU0MGE0ZmQxNGZl'
                                . 'M2U2MDZhY2EyZjRiNzFjMjBiYTRjYWEwMWQ4M2ZiMTBhMTVkYzRmNDMzN2EifQ==',
                        'create_on' => '2016-12:21 10:00'
                ]
        ],
        'roles' => [
                [
                        'id' => 1,
                        'title' => 'superadmin',
                        'role_level' => 10,
                        'is_system' => 1,
                        'is_admin' => 1,
                        'is_active' => 1,
                        'create_on' => '2016-12:21 10:00'
                ],
                [
                        'id' => 2,
                        'title' => 'administrator',
                        'role_level' => 20,
                        'is_admin' => 1,
                        'is_active' => 1,
                        'create_on' => '2016-12:21 10:00'
                ],
                [
                        'id' => 3,
                        'title' => 'support',
                        'role_level' => 30,
                        'is_admin' => 1,
                        'is_active' => 1,
                        'create_on' => '2016-12:21 10:00'
                ],
                [
                        'id' => 4,
                        'title' => 'visitor',
                        'role_level' => 999,
                        'is_active' => 1,
                        'create_on' => '2016-12:21 10:00'
                ]
        ],
        'admins' => [
                [
                        'id' => 1,
                        'username' => 'superadmin',
                        'password' => Hash::make('superadmin'),
                        'email' => 'su@webmail.com', 'is_system' => 0,
                        'is_system' => 1,
                        'create_on' => '2016-12:21 10:00'
                ],
                [
                        'id' => 2, 'username' => 'administrator',
                        'password' => Hash::make('administrator'),
                        'email' => 'ad@webmail.com',
                        'is_system' => 1,
                        'create_on' => '2016-12:21 10:00'
                ],
                [
                        'id' => 3,
                        'username' => 'support',
                        'password' => Hash::make('support'),
                        'email' => 'sr@webmail.com',
                        'is_system' => 1,
                        'create_on' => '2016-12:21 10:00'
                ]
        ],
        'admin_roles' => [
                ['admin_id' => 1, 'role_id' => 1],
                ['admin_id' => 2, 'role_id' => 2],
                ['admin_id' => 3, 'role_id' => 3]
        ],
        'permissions' => [
                ['role_id' => 1, 'module' => 'system', 'panel' => 'admins', 'permission' => 'create', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'admins', 'permission' => 'edit', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'admins', 'permission' => 'activeness', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'admins', 'permission' => 'index', 'create_on' => '2016-12:21 10:00'],

                ['role_id' => 1, 'module' => 'system', 'panel' => 'roles', 'permission' => 'create', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'roles', 'permission' => 'edit', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'roles', 'permission' => 'activeness', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'roles', 'permission' => 'index', 'create_on' => '2016-12:21 10:00'],

                ['role_id' => 1, 'module' => 'system', 'panel' => 'menus', 'permission' => 'create', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'menus', 'permission' => 'edit', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'menus', 'permission' => 'activeness', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'menus', 'permission' => 'index', 'create_on' => '2016-12:21 10:00'],

                ['role_id' => 1, 'module' => 'sample', 'panel' => 'samples', 'permission' => 'create', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'sample', 'panel' => 'samples', 'permission' => 'edit', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'sample', 'panel' => 'samples', 'permission' => 'index', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'sample', 'panel' => 'samples', 'permission' => 'upload', 'create_on' => '2016-12:21 10:00'],
        ]
];