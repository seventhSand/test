<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/14/2016
 * Time: 5:14 PM
 */

namespace Webarq\Laravel\Extend;


use Blade;

class BladeLaravel
{
    public function __construct()
    {
        Blade::extend(function($value) {
            return preg_replace('/\{\?(.+)\?\}/s', '<?php ${1} ?>', $value);
        });
    }
}