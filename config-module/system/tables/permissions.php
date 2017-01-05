<?php
/**
 * Created by Daniel Simangunsong
 * Date: 14/05/2015
 * Time: 11:25
 *
 * Calm seas, never make skill full sailors
 *
 * This table should not have translate columns
 */

return [
        ['type' => 'int', 'name' => 'role_id', 'length' => 11, 'unsigned' => true, 'uniques' => true],
        ['master' => 'shortLabel', 'name' => 'module', 'uniques' => true],
        ['master' => 'shortLabel', 'name' => 'panel', 'uniques' => true],
        ['master' => 'shortLabel', 'name' => 'permission', 'uniques' => true],
        ['master' => 'createOn']
];