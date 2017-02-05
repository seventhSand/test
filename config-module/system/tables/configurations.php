<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/15/2016
 * Time: 3:09 PM
 */

return [
        ['master' => 'id'],
        ['master' => 'label', 'name' => 'module', 'uniques' => true],
        ['master' => 'longLabel', 'name' => 'key', 'uniques' => true],
        ['master' => 'shortIntro', 'name' => 'setting', 'notnull' => true],
        ['master' => 'createOn'],
        ['master' => 'lastUpdate']
];