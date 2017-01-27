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
use Webarq\Info\ModuleInfo;
use Webarq\Info\PanelInfo;

class BaseController extends Webarq
{
    /**
     * @var \Webarq\Manager\AdminManager
     */
    protected $admin;

    /**
     * @inheritdoc
     */
    protected $themes;

    /**
     * @param string $controller
     * @param string $module
     * @param string $panel
     * @param string $action
     * @param array $params
     */
    public function __construct($controller, $module, $panel, $action, array $params = [])
    {
        $this->themes = config('webarq.system.themes', 'default');

        parent::__construct($controller, $module, $panel, $action, $params);

        $this->admin = Auth::user();

        view()->share('admin', $this->admin);
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
        } elseif (null !== \Request::segment(2) && (
                        !$this->isAccessible() || !is_object($this->module) || !is_object($this->panel))
        ) {
            return $this->actionGetForbidden();
        }

        return parent::before();
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

    /**
     * @return mixed
     */
    protected function isAccessible()
    {
        return $this->module instanceof ModuleInfo && $this->panel instanceof PanelInfo
        && Wa::panel()->isAccessible($this->module, $this->panel, $this->action);
    }

    /**
     * @return string
     */
    public function actionGetIndex()
    {
        $this->layout->{'rightSection'} = 'You are in home';
    }
}