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
     * @inheritdoc
     */
    protected $themes = 'gentella';

    /**
     * @param string $controller
     * @param string $module
     * @param string $panel
     * @param string $action
     * @param array $params
     */
    public function __construct($controller, $module, $panel, $action, array $params = [])
    {
        $this->themes = config('webarq.system.themes', $this->themes);

        parent::__construct($controller, $module, $panel, $action, $params);

        $this->admin = Auth::user();

        \View::share('admin', $this->admin);
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
        } elseif (!Wa::panel()->isAccessible($this->getModule(), $this->getPanel(), $this->action)) {
            return $this->actionGetForbidden();
        } elseif (!is_object($this->module) || !is_object($this->panel)) {
            return $this->actionGetForbidden();
        }

        return parent::before();
    }

    public function actionGetIndex()
    {
        return 'Content not available';
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