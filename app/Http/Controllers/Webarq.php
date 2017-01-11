<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/19/2016
 * Time: 10:13 AM
 */

namespace App\Http\Controllers;


use Wa;

class Webarq extends Controller
{
    /**
     * Called class
     *
     * @var string
     */
    protected $controller;

    /**
     * @var object|string
     */
    protected $module;

    /**
     * @var string Panel name
     */
    protected $panel;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @param string $controller
     * @param string $module
     * @param string $panel
     * @param string $action
     * @param array $params
     */
    public function __construct($controller, $module, $panel, $action, array $params = [])
    {
        $this->controller = $controller;
        $this->setModule($module);
        $this->setPanel($panel);
        $this->action = $action;
        $this->params = $params;
    }

    /**
     * Set module with an object of Webarq\Info\ModuleInfo
     * If module not exist in configuration, string $module will be used
     *
     * @param string $module Module name
     */
    protected function setModule($module)
    {
        $this->module = Wa::module($module) ?: $module;
    }

    /**
     * Get module name
     *
     * @return object|string
     */
    protected function getModule()
    {
        return is_object($this->module) ? $this->module->getName() : $this->module;
    }

    /**
     * Set panel with an object of Webarq\Info\PanelInfo
     * If module not exists in configuration, string $panel will be used
     *
     * @param string $panel
     */
    protected function setPanel($panel)
    {
        $this->panel = is_object($this->module) ? $this->module->getPanel($panel, $panel) : $panel;
    }

    /**
     * Get panel name
     *
     * @return string
     */
    protected function getPanel()
    {
        return is_object($this->panel) ? $this->panel->getName() : $this->panel;
    }

    /**
     * Get params value by key
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    protected function getParam($key, $default = null)
    {
        return array_get($this->params, $key, $default);
    }

    /**
     * Called from rotu
     * @return mixed
     */
    public function before()
    {
        if ('POST' == \Request::method() && [] === \Request::input()) {
            return $this->actionGetNoMethod();
        }
    }

    public function actionGetNoMethod()
    {
        return view('webarq.errors.405');
    }

    public function actionGetForbidden()
    {
        return view('webarq.errors.403');
    }

}