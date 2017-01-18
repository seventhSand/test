<?php
/**
 * Created by PhpStorm
 * Date: 21/10/2016
 * Time: 8:38
 * Author: Daniel Simangunsong
 *
 * Note.
 *
 */

return [
        'title' => 'System',
        'tables' => [
                'histories', 'permissions', 'configurations', 'admins', 'admin_roles', 'roles', 'menus'
        ],
        'panels' => [
                'dashboard' => [
// When not set, will use system determination which is return helper/listing/index/systems/dashboard
// True will return systems/admins/listing
                        'permalink' => 'system/dashboard',
                        'class' => 'dashboard',
// When not set, will translate group name
                        'title' => 'Dashboard',
// Disable listing
                        'listing' => false,
                ],
                'configurations' => [
                        'permalink' => true,
                        'listing' => false,
                ],
                'admins' => [
                        'permalink' => null,
// Panel allowed action
                        'actions' => [
                                'activeness',
                                'create' => [
// Permission should be an array, but its okay to set it as string when you have just one item
// By default this permission will check with OR operator, mean when admin have any one of these,
// then it will be passed in validator manager. Assign true in to last item, to force admin
// having all permissions
                                        'permissions' => [
                                                'is_system', 'activeness'
                                        ],
// Actions rules if any. This will be checking on routes while possible, or on admin base controller, or on
// the related controller it self
                                        'rules' => [
                                        ],
// Transaction form if any
                                        'form' => [
                                                'title' => 'Create Admins',
// Following by input key => attributes
// Input key should be following "moduleName.tableName.columnName" format name
                                                'system.admins.username' => [
                                                        'length' => '100',
// Input rules:
//                                                        'rules' => ''
                                                ],
                                                'system.admins.password' => [
                                                        'type' => 'password',
                                                        'modifier' => 'password'
                                                ],
                                                'system.admins.email' => [
                                                        'class' => 'email',
                                                        'rules' => 'email'
                                                ],
                                                'system.admin_roles.role_id' => [
                                                        'title' => 'Role',
                                                        'type' => 'select',
                                                        'multiple',
                                                        'options' => [
                                                                1 => 'Superadmin',
                                                                2 => 'Administrator'
                                                        ],
                                                        'rules' => 'required|array'
                                                ],
                                                'system.admins.create_on' => [
                                                        'protected' => 'on',
                                                        'modifier' => 'datetime'
                                                ]
                                        ]
                                ],
                                'edit',
                                'delete',
                                'is_system'
                        ]
                ],
                'roles',
                'menus' => [
                        'actions' => [
                                'create' => [
                                        'form' => [
                                                'system.menus.title',
                                                'system.menus.permalink' => [
                                                        'type' => 'text',
                                                ],
                                                'system.menus.parent_id' => [
// Allow system to build select input, and get options from mentioned table
                                                        'type' => 'select table',
                                                        'title' => 'Parent Menu',
                                                        'source-table' => [
// Table name, while not set will get current input table
                                                                'name' => 'menus',
// Column for select option value, and select option label
                                                                'column' => ['id', 'title']
                                                        ],
                                                        'blankOption' => [0 => 'This is a parent menu'],

                                                ],
                                        ]
                                ]
                        ]
                ],
        ],
];