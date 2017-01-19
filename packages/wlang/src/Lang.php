<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/16/2017
 * Time: 1:34 PM
 */

namespace Wlang;


class Lang
{
    /**
     * System language
     *
     * @var string
     */
    protected $system = 'en';

    /**
     * Default language
     *
     * @var string
     */
    protected $default = 'en';

    public function getSystem()
    {
        return $this->system;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function getCodes()
    {
        return ['en', 'id', 'fr'];
    }
}