<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/24/2017
 * Time: 2:44 PM
 */

return [
        ['master' => 'id'],
        ['master' => 'label', 'name' => 'title', 'multilingual' => true],
        ['master' => 'label', 'name' => 'file', 'multilingual' => true],
        ['master' => 'description', 'multilingual' => true],
        ['master' => 'sequence'],
        'timestamps' => true,
        'model' => 'media',
        'model-dir' => 'test'
];