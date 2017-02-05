<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/24/2017
 * Time: 3:03 PM
 */

return [
        'title' => 'Samples',
        'tables' => ['samples'],
        'panels' => [
// How to name our panels key member:
// 1. tableName => [settings]
// 2. title => ["table" => "tableName"], ... another settings]
// 3. title:tableName => [settings]
// and we use the third way on this example
                'sample:samples' => [
// Instead of using key as the title, just set the name that fitting your need
//                        'title' => 'Sample',
                        'listing' => [
//                                'table' => 'samples',
                                'headers' => [
                                        'columns' => [
                                                'title',
                                                'file' => [
// Modify value before rendering. All modifier should be
// registering in class Webarq\Manager\ValueModifierManager
                                                        'modifier' => 'thumb',
// Assign head column title
                                                        'title' => 'File',
// Column is selected, but not shown on the list
//                                                        'guarded' => true
                                                ]
                                        ],
// Add container head (normally is thead)
                                        'container' => 'thead'
                                ],
                                'filter' => [

                                ],
// Default listing sequence, give array for multiple column sequence
                                'sequence' => 'sequence',
// Searchable column, give array for multiple column sequence
                                'searchable' => 'title',
// Set as an array in [limit, view file name] format
                                'pagination' => 100
// Enable data driver
//                                'driver' => ['json'],
                        ],
// Is panel guarded and check if current admin has the permissions
// to accessing the panel it self
// By default it will be set in to true
                        'guarded' => true,
// Panel allowed action
                        'actions' => [
                                'activeness' => [
// Button permission, if multiple permissions is needed, then set it as an numeric array
// When multiple permissions is given, and we needed all permissions to be granted,
// then add boolean true as the last item
                                        'permissions' => ['activeness'],
// Button rules, for callback item will use two parameter (Object Admin, Array Items)
// To get row (item) value, then key should be prefixed with "item.", and "admin." to get
// admin attributes.
// To change logic operator (eg. "=, !=") then set the keys value as an array in
// [known logic operator, value] format
//                                        'rules' => [
//                                                'item.is_system' => 0
//                                        ],
// Button position location, automatically registered to the listing when not set
                                        'placement' => 'listing',
// Button HTML attributes
                                        'attributes' => [],
// Button container view
                                        'container-view' => ''
                                ],
                                'create' => [
// Transaction form if any
                                        'form' => [
// Add attribute form
                                                'attributes' => [
                                                        'enctype' => 'multipart/form-data'
                                                ],
// Add form title
                                                'title' => 'Create Sample',
// Following by input key => attributes
// Input key should be following "moduleName.tableName.columnName" format name
                                                'sample.samples.title' => [
                                                        'length' => '100',
                                                        'error-messages' => [
                                                                'required' => 'Should be blank'
                                                        ],
// Multilingual input
// 1      : will inherited source input rules
// true   : will ignored source input rules
// string : in laravel format
// array  : will overwrite property
//                                                        'multilingual' => 1,
// Added input information
//                                                        'info' => 'Some info here'
                                                ],
                                                'sample.samples.file' => [
                                                        'permissions' => 'upload',
// Value for impermissible input
                                                        'impermissible' => '',
                                                        'file' => [
                                                                'type' => 'image',
                                                                'mimes' => ['jpg', 'jpeg', 'png'],
                                                                'max' => 1024,
                                                                'upload-dir' => 'site/uploads/media',
                                                                'resize' => [
                                                                        'width' => 200,
                                                                        'height' => 200,
                                                                ]
                                                        ]
                                                ],
                                                'sample.samples.description',
                                                'sample.samples.sequence' => [
// Do not show input on the form
//                                                        'invisible' => true
                                                ],
                                        ]
                                ],
                                'edit' => [
// Transaction form if any
                                        'form' => [
// Data remote getter model
//                                                'model' => true,
// Add attribute form
                                                'attributes' => [
                                                        'enctype' => 'multipart/form-data'
                                                ],
// Add form title
                                                'title' => 'Edit Sample',
// Following by input key => attributes
// Input key should be following "moduleName.tableName.columnName" format name
                                                'sample.samples.title' => [
                                                        'length' => '100'
                                                ],
                                                'sample.samples.file' => [
                                                        'permissions' => 'upload',
// Un-required input, "required" key could be replaced with "notnull"
                                                        'required' => false,
// Value for impermissible input
//                                                        'impermissible' => 'some-value',
                                                        'file' => [
                                                                'type' => 'image',
                                                                'mimes' => ['jpg', 'jpeg', 'png'],
                                                                'max' => 1024,
                                                                'upload-dir' => 'site/uploads/media',
                                                                'resize' => [
                                                                        'width' => 200,
                                                                        'height' => 200,
                                                                ]
                                                        ],
// Ignored when field is empty
                                                        'ignored' => true
                                                ],
                                                'sample.samples.description',
                                                'sample.samples.sequence'
                                        ]
                                ],
                                'delete',
                                'export' => [
                                        'placement' => ['header', 'listing'],
// Limit options, number of limit or array of [offset, number of limit]
//                                        'limit' => 10,
//                                        'columns' => ['id'],
//                                        'where' => ['id' => '3'],
// Raw data getter model (string model name or a callback)
//                                        'model' => 'modelName',
                                ],
                        ]
                ],
        ]
];