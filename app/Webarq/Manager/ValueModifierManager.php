<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/24/2017
 * Time: 4:40 PM
 */

namespace App\Webarq\Manager;


class ValueModifierManager extends \Webarq\Manager\ValueModifierManager
{
    public function __construct()
    {
        parent::__construct();

//        static::macro('thumb', function($path, $width = '30'){
//            return '<img src="' . \URL::asset($path) . '" style="width:' . $width .'px;"/>';
//        });
    }
}