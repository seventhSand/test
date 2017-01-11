<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/15/2016
 * Time: 10:40 AM
 */

namespace App\Http\Controllers\Panel;


use App\Http\Controllers\Webarq;
use Auth;
use URL;
use Wa;

class BaseController extends Webarq
{
    /**
     * @var \Webarq\Manager\AdminManager
     */
    protected $admin;

    /**
     * @var object View
     */
    protected $layout = 'webarq.layout.panel';

    /**
     * @param string $controller
     * @param string $module
     * @param string $panel
     * @param string $action
     * @param array $params
     */
    public function __construct($controller, $module, $panel, $action, array $params = [])
    {
        parent::__construct($controller, $module, $panel, $action, $params);

        $this->admin = Auth::user();
    }

    /**
     * Called from routing file
     *
     * @return mixed
     */
    public function before()
    {
        $this->setModulePanel();

        if (!isset($this->admin)) {
            if ('login' !== $this->action && 'auth' !== $this->controller) {
                return redirect(URL::panel('system/admins/auth/login'));
            }
        } elseif (!$this->hasPermission()) {
            return $this->actionGetForbidden();
        } elseif (!is_object($this->module) || !is_object($this->panel)) {
            return $this->actionGetForbidden();
        }

        $this->setLayout($this->layout);

        $this->layout->panel = Wa::instance('manager.cms.panel', $this->admin)->generateMenu();

        return parent::before();
    }

    /**
     * Check if admin has permission accessing current url
     *
     * @return mixed
     */
    protected function hasPermission()
    {
        $path = strtolower($path = $this->getModule() . '.' . $this->getPanel() . '.' . $this->action);

        return $this->admin->hasPermission($path);
    }

    /**
     * @param $view
     */
    protected function setLayout($view)
    {
        $this->layout = view($view);
    }

    public function actionGetIndex()
    {
        return 'Index';
    }

    /**
     * Called from routing file
     *
     * @return object
     */
    public function after()
    {
        return $this->layout;
    }

    /**
     * Set module panel from active url params
     */
    protected function setModulePanel()
    {
        if (!is_object($this->module)) {
            $this->setModule($this->getParam(1));
        }

        if (!is_object($this->panel)) {
            $this->setPanel($this->getParam(2));
        }
    }
}