<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/16/2017
 * Time: 1:34 PM
 */

namespace Webarq\Lang;


class Wl
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

    /**
     * @var array
     */
    protected $langCodeColumn = ['name' => 'lang_code', 'type' => 'char', 'length' => 2, 'notnull' => true];

    /**
     * @return string
     */
    public function getSystem()
    {
        return $this->system;
    }

    /**
     * @return string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return array
     */
    public function getCodes()
    {
        return ['en', 'id'];
    }

    /**
     * @param null $key
     * @param null $default
     * @return array|mixed
     */
    public function getLangCodeColumn($key = null, $default = null)
    {
        return null === $key ? $this->langCodeColumn : array_get($this->langCodeColumn, $key, $default);
    }

    /**
     * @param $table
     * @return string
     */
    public function translateTableName($table)
    {
        return $table . '_i18n';
    }
}