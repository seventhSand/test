<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/5/2017
 * Time: 1:18 PM
 */

namespace Webarq\Manager;


use Illuminate\Support\Traits\Macroable;

class ValueModifierManager
{
    use Macroable;

    public function __construct()
    {
        static::macro('password', function($string){
            return \Hash::make($string);
        });

        static::macro('datetime', function($time = null){
            return date('Y-m-d H:i:s', strtotime($time) ?: time());
        });

        static::macro('date', function($time = null){
            return date('Y-m-d', strtotime($time) ?: time());
        });

        static::macro('time', function($time = null){
            return date('H:i:s', strtotime($time) ?: time());
        });
    }
}