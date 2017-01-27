<?php
/**
 * Created by PhpStorm
 * Date: 18/12/2016
 * Time: 11:03
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Info;


use Webarq\Manager\setPropertyManagerTrait;

/**
 * Helper class
 *
 * Class PanelInfo
 * @package Webarq\Info
 */
class PanelInfo
{
    use SetPropertyManagerTrait;

    /**
     * Panel name
     * Used when generate panel anchor <a/> html tag
     *
     * @var
     */
    protected $name;

    /**
     * Panel permalink
     * Used when generate panel anchor <a/> html tag
     *
     * @var mixed
     */
    protected $permalink;

    /**
     * Panel title
     *
     * Used when generate panel anchor <a/> html tag
     *
     * @var
     */
    protected $title;

    /**
     * Panel listing configuration
     *
     * @var array
     */
    protected $listing = [];

    /**
     * Panel actions configuration
     *
     * Used when generate panel button in listing
     *
     * @var array
     */
    protected $actions = [];

    /**
     * Panel attributes.
     *
     * Used when generate panel anchor <a/> html tag
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Panel module
     *
     * @var
     */
    protected $module;

    /**
     * Is panel guarded
     *
     * @var bool
     */
    protected $guarded = true;

    /**
     * Create PanelInfo instance
     *
     * @param string $name Panel name
     * @param string $module Panel Module
     * @param array $options Panel options
     */
    public function __construct($name, $module, array $options)
    {
        $this->name = $name;

        $this->module = $module;

        $this->setPropertyFromOptions($options, true);

        $this->attributes = $options;

        if ([] !== $this->actions) {
            foreach ($this->actions as $key => $options) {
                if (is_numeric($key)) {
                    unset($this->actions[$key]);
                    $this->actions[$options] = [];
                }
            }
        }
    }

    /**
     * Get panel action
     *
     * @param null|string $key
     * @param null $default
     * @return mixed
     */
    public function getAction($key = null, $default = null)
    {
        return array_get($this->actions, $key, $default);
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Get panel title
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title ?: ucfirst($this->name);
    }

    /**
     * @return array
     */
    public function getListing()
    {
        return $this->listing;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get panel permalink, auto generate url panel when action is set
     *
     * @param null|string $action
     * @return mixed
     */
    public function getPermalink($action = null)
    {
        return is_null($action)
                ? $this->permalink
                : \URL::panel(\URL::detect($this->permalink, $this->module, $this->name, $action));
    }

    public function isGuarded()
    {
        return true === $this->guarded;
    }
}