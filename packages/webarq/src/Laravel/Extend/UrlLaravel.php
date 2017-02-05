<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/15/2016
 * Time: 2:25 PM
 */

namespace Webarq\Laravel\Extend;


use URL;
use Wa;

class UrlLaravel
{
    public function __construct()
    {
        /**
         * Generate an absolute URL to given path, and prefixing it by  webarq config panel url prefix
         *
         * @param mixed $str
         */
        URL::macro('panel', function ($str, $attr = [], $secure = null) {
            if ($this->isValidUrl($str)) {
                return $str;
            }

            return $this->to(config('webarq.system.panel-url-prefix') . '/' . $str,
                    $attr, $secure ?: config('webarq.system.secureUrl'));
        });

        /**
         * Generate an absolute URL to given path
         *
         * @param mixed $str
         */
        URL::macro('site', function ($str, $attr = [], $secure = null) {
            if ($this->isValidUrl($str)) {
                return $str;
            }

            return $this->to($str, $attr, $secure ?: config('webarq.system.secureUrl'));
        });

        URL::macro('detect', function ($url, $module, $panel, $item) {
            if (true === $url) {
                $url = trim($module . '/' . $panel . '/' . $item, '/');
            } elseif (is_null($url)) {
                $url = 'helper/' . trim($item, '/') . '/' . $module . '/' . $panel;
            }

            return str_replace('//', '/', $url);
        });
    }
}