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

        static::macro('datetime', function(){
            return date('Y-m-d H:i:s');
        });

        static::macro('date', function(){
            return date('Y-m-d');
        });

        static::macro('time', function(){
            return date('H:i:s');
        });
    }
}