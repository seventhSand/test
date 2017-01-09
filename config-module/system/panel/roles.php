<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 11/29/2016
 * Time: 12:57 PM
 */

return [
        'permalink' => null,
// When not set, will translate group name
        'label' => 'RoleQ',
        'listing' => [
                'headers' => [
                        'columns' => [
                                'role_level' => ['label' => 'LabelQ'],
                                'title',
                                'is_admin',
                                'is_active',
                                'is_system' => [
// @todo Column selected but not showing on the list
                                        'guarded' => true
                                ]
                        ]
                ],
// Default listing sequence, give array for multiple column sequence
                'sequence' => 'label',
// Searchable column, give array for multiple column sequence
                'searchable' => 'label',
// Set as an array in [limit, view file name] format
                'pagination' => 3,
        ],
// Panel allowed action
        'actions' => [
                'activeness' => [
                        'permissions' => 'activeness',
                        'rules' => [
                                'item.is_system' => 0
                        ],
// Set button position location
                        'placement' => 'listing'
                ],
                'create' => [
// Transaction form if any
                        'form' => [
                                'title' => 'Create Role',
// Following by input key => attributes
// Input key should be following "moduleName.tableName.columnName" format name
                                'system.roles.role_level' => [
                                        'name' => 'level',
                                        'type' => 'text',
                                        'label' => 'Level',
                                        'rules' => 'max:255|min:10',
                                        'error-message' => [
                                                'required' => 'Role level should not be empty'
                                        ],
                                        'info' => 'For best practice, please use simple number which is easy '
                                                . 'to remember. Eg 10, 20, ...',
                                ],
                                'system.roles.title',
                                'system.roles.is_admin' => [
                                        'required'
                                ],
                                'system.roles.is_active' => [
// Mean current login admin must have activeness permission
                                        'permissions' => 'activeness',
// Guarded value, use when input permission not fulfilled
                                        'guarded-value' => 0
                                ],
                                'system.roles.is_system'
                        ]
                ],
                'edit' => [
// Button rules
                        'rules' => [
                                'admin.level' => ['>=', 'item.role_level']
                        ],
// Transaction form if any
                        'form' => [
                                'title' => 'Create Role',
// Following by input key => attributes
// Input key should be following "moduleName.tableName.columnName" format name
                                'system.roles.role_level' => [
                                        'name' => 'level',
                                        'type' => 'text',
                                        'label' => 'Level',
                                        'rules' => 'max:255|min:10',
                                        'error-message' => [
                                                'required' => 'Role level should not be empty'
                                        ],
                                        'info' => 'For best practice, please use simple number which is easy '
                                                . 'to remember. Eg 10, 20, ...',
                                ],
                                'system.roles.title',
                                'system.roles.is_admin' => [
                                        'required'
                                ],
                                'system.roles.is_active' => [
// Mean current login admin must have activeness permission
                                        'permissions' => 'activeness',
// Guarded value, use when input permission not fulfilled
                                        'guarded-value' => 0
                                ],
                                'system.roles.is_system'
                        ]
                ],
                'delete',
                'is_system'
        ]
];