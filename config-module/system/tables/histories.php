<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/5/2017
 * Time: 3:56 PM
 */

return [
        ['master' => 'id'],
        ['master' => 'smallInt', 'name' => 'role_level', 'notnull' => true],
        ['master' => 'shortLabel', 'name' => 'action', 'notnull' => true],
        ['master' => 'shortLabel', 'name' => 'actor', 'notnull' => true],
        ['master' => 'label', 'name' => 'table_name', 'notnull' => true],
        ['master' => 'bigInt', 'name' => 'table_id', 'notnull' => true],
        ['master' => 'longDescription', 'name' => 'properties', 'notnull' => true],
        ['master' => 'createOn']
];