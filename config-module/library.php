<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/30/2017
 * Time: 7:26 PM
 */

return [
    'config' => [
        'image' => [
            'mimes' => ['jpeg', 'jpg', 'gif'],
            'size' => 5*1024*1024
        ]
    ],
    'panels' => [
        'libraries' => [
            'actions' => [
                'upload',
                'delete'
            ]
        ]
    ]
];