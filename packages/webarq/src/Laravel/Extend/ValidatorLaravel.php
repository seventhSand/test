<?php
/**
 * Created by PhpStorm
 * Date: 30/12/2016
 * Time: 21:58
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Laravel\Extend;


use Validator;

class ValidatorLaravel
{
    public function __construct()
    {
        Validator::extend('numericArray', function($attribute, array $values, $parameters, $validator)
        {
            foreach ($values as $value) {
                if (!is_numeric($value)) {
                    return false;
                }
            }
            return true;
        });

        Validator::extend('integerArray', function($attribute, array $values, $parameters, $validator)
        {
            foreach ($values as $value) {
                if (!is_int($value)) {
                    return false;
                }
            }
            return true;
        });


        Validator::extend('maxArray', function($attribute, array $values, $parameters, $validator)
        {
            foreach ($values as $value) {
                if ($value > $parameters[0]) {
                    return false;
                }
            }
            return true;
        });


        Validator::extend('minArray', function($attribute, array $values, $parameters, $validator)
        {
            foreach ($values as $value) {
                if ($value < $parameters[0]) {
                    return false;
                }
            }
            return true;
        });

    }
}