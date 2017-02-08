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
                        'guarded' => false
                ],
                'configurations' => [
                        'type' => 'configuration',
                        'actions' => [
                                'edit' => [
                                        'form' => [
                                                'attributes' => [
                                                        'enctype' => 'multipart/form-data'
                                                ],
                                                'favicon' => [
                                                        'info' => 'Please use image in 16px X 16px dimension',
                                                        'file' => [
                                                                'type' => 'image',
                                                                'mimes' => ['png', 'ico'],
                                                                'max' => 1024,
                                                                'file-name' => 'favicon',
                                                                'resize' => [
                                                                        'width' => 16,
                                                                        'height' => 16
                                                                ]
                                                        ]
                                                ],
                                                'meta_title' => [
                                                        'type' => 'text',
                                                        'rules' => 'required|max:20'
                                                ],
                                                'meta_description' => [
                                                        'type' => 'textarea',
                                                        'rules' => 'required'
                                                ],
                                                'online' => [
                                                        'type' => 'select',
                                                        'options' => ['Offline', 'Online'],
                                                ]
                                        ]
                                ]
                        ]
                ],
                'admins' => [
                        'permalink' => null,
                        'listing' => [
                                'headers' => [
                                        'columns' => ['username', 'email', 'is_system', 'is_active']
                                ],
                        ],
// Panel allowed action
                        'actions' => [
                                'activeness',
                                'create' => [
// Permission should be an array, but its okay to set it as string when you have just one item
// By default these permissions will be validated with OR operator, its mean when admin have any one of them,
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
                                                        'type' => 'select table',
                                                        'source-table' => [
                                                                'name' => 'roles',
                                                                'column' => ['id', 'title'],
                                                        ],
                                                        'multiple',
                                                        'rules' => 'required|array'
                                                ]
                                        ]
                                ],
                                'edit' => [
// Permission should be an array, but its okay to set it as string when you have just one item
// By default this permission will check with OR operator, mean when admin have any one of these,
// then it will be passed in validator manager. Assign true in to last item, to force admin
// having all permissions
                                        'permissions' => ['is_system', 'activeness'],
                                        'rules' => [],
                                        'form' => [
                                                'title' => 'Edit Admins',
                                                'system.admins.username' => [
                                                        'length' => '100',
                                                        'name' => 'user-id',
                                                        'readonly'
                                                ],
                                                'system.admins.password' => [
                                                        'type' => 'password',
                                                        'modifier' => 'password',
                                                        'notnull' => false,
                                                        'ignored' => true
                                                ],
                                                'system.admins.email' => [
                                                        'class' => 'email',
                                                        'rules' => 'email'
                                                ],
                                                'system.admin_roles.role_id' => [
                                                        'title' => 'Role',
                                                        'type' => 'select table',
                                                        'source-table' => [
                                                                'name' => 'roles',
                                                                'column' => ['id', 'title']
                                                        ],
                                                        'multiple',
                                                        'rules' => 'required|array',
                                                ]
                                        ]
                                ],
                                'delete',
                                'is_system'
                        ]
                ],
                'roles',
                'menus' => [
                        'type' => 'listing',
                        'actions' => [
                                'create' => [
                                        'form' => [
                                                'attributes' => [
                                                        'enctype' => 'multipart/form-data',
                                                ],
                                                'system.menus.parent_id' => [
                                                        'name' => 'parental',
// Allow system to build select input, and get options from mentioned table
                                                        'type' => 'select table',
                                                        'title' => 'Parent Menu',
                                                        'source-table' => [
// Table name, while not set will get current input table
                                                                'name' => 'menus',
// Column for select option value, and select option label
                                                                'column' => ['id', 'title']
                                                        ],
                                                        'blank-option' => [0 => 'This is a parent menu'],

                                                ],
                                                'system.menus.title' => [
                                                        'referrer' => '.seo-url'
                                                ],
                                                'system.menus.template',
                                                'system.menus.permalink' => [
                                                        'type' => 'text',
                                                        'class' => 'seo-url'
                                                ],
                                                'system.menus.sequence' => [
                                                        'parent-column' => 'parental'
                                                ]
                                        ]
                                ],
                                'edit' => [
                                        'form' => [
                                                'attributes' => [
                                                        'enctype' => 'multipart/form-data',
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
                                                        'blank-option' => [0 => 'This is a parent menu'],

                                                ],
                                                'system.menus.title' => [
                                                        'referrer' => '.seo-url'
                                                ],
                                                'system.menus.template',
                                                'system.menus.permalink' => [
                                                        'type' => 'text',
                                                        'class' => 'seo-url'
                                                ],
                                                'system.menus.sequence' => [
                                                        'parent-column' => 'parent_id'
                                                ]
                                        ]
                                ],
                                'activeness',
                                'delete'
                        ]
                ]
        ],
];